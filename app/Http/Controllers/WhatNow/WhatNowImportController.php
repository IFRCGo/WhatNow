<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Importer\BulkUploadTemplateExport;
use App\Classes\RcnApi\Importer\Exceptions\RcnExportException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportInvalidFileException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportWillOverwriteException;
use App\Classes\RcnApi\Importer\RcnExporter;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Importer\RcnImporter;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Http\Controllers\ApiController;
use App\Rules\CsvEventTypeValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\Csv\AbstractCsv;
use League\Csv\CannotInsertRecord;
use League\Csv\Reader;

final class WhatNowImportController extends ApiController
{

    private $client;


    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->whatnow();
    }


    public function import(Request $request, string $countryCode, string $languageCode)
    {
        try{
            $this->validate($request, [
                'csv' => ['file', 'mimes:csv,txt'],
            ]);
        }catch (ValidationException $e)
        {
            return new JsonResponse([
                'message' => $e->errors()['csv'][0],
                'errorCode' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $importer = new RcnImporter($this->client);

        if ($request->get('warnings') === "false") {
            $importer->turnWarningsOff();
        }

        if ($request->get('overwrite') === "true") {
            $importer->turnOverwritingOn();
        }

        $file = $request->file('csv');
        $csv = Reader::createFromPath($file->path(), 'r');

        try {
            $importer->importCsv($csv, $countryCode, $languageCode);
        } catch (RcnImportInvalidFileException $e) {
            return new JsonResponse([
                'message' => trans('rcnapi.bad_request'),
                'errorCode' => $e->getErrorCode()
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (RcnImportWillOverwriteException $e) {
            return $this->respondWithConflict($e);
        } catch (RcnImportException $e) {
            return $this->respondWithError($e);
        }

        return new JsonResponse($importer->getReport(), JsonResponse::HTTP_OK);
    }


    public function exportBlank(Request $request,string $countryCode)
    {
        $extension = $request->query('extension', 'csv');
        $exporter = new RcnExporter($this->client);
        if (strlen($countryCode) !== 3) {
            return new JsonResponse(['message' => 'Invalid country code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            return $exporter->buildTemplate($countryCode,$extension);
        } catch (CannotInsertRecord $e) {
            return $this->respondWithError($e, $e->getMessage());
        }
    }


    public function export(string $countryCode, string $languageCode)
    {
        if (strlen($countryCode) !== 3) {
            return new JsonResponse(['message' => 'Invalid country code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (strlen($languageCode) !== 2) {
            return new JsonResponse(['message' => 'Invalid language code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->authorize('listDrafts', Instruction::class);

        $exporter = new RcnExporter($this->client);

        try {
            $instructionRows = $exporter->buildInstructionRows($countryCode, $languageCode);
            $attributionRow = $exporter->buildAttributionRow($countryCode, $languageCode);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        } catch (RcnExportException $e) {
            return $this->respondWithError($e, $e->getMessage());
        }

        try {
            $csv = $exporter::buildCsvTemplate($countryCode, $instructionRows, $attributionRow);
        } catch (CannotInsertRecord $e) {
            return $this->respondWithError($e, $e->getMessage());
        }

        $csv->output();
    }
}
