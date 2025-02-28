<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Importer\BulkUploadTemplateExport;
use App\Classes\RcnApi\Importer\BulkUploadTemplateImport;
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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use League\Csv\AbstractCsv;
use League\Csv\CannotInsertRecord;
use League\Csv\Reader;
use Maatwebsite\Excel\Excel;
use function Maatwebsite\Excel\Facades\Excel;

/**
 * @OA\Tag(
 *     name="WhatNowImport",
 *     description="Operations about bulk upload"
 * )
 */
final class WhatNowImportController extends ApiController
{

    private $client;

    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->whatnow();
    }

    /**
     * @OA\Post(
     *     path="/import/{countryCode}/{languageCode}",
     *     tags={"WhatNowImport"},
     *     summary="Import a CSV file",
     *     description="Imports a CSV file for a specific country and language",
     *     operationId="importCsv",
     *     @OA\Parameter(
     *         name="countryCode",
     *         in="path",
     *         required=true,
     *         description="Country code for the import",
     *         @OA\Schema(type="string", example="USA")
     *     ),
     *     @OA\Parameter(
     *         name="languageCode",
     *         in="path",
     *         required=true,
     *         description="Language code for the import",
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"csv"},
     *                 @OA\Property(
     *                     property="csv",
     *                     type="string",
     *                     format="binary",
     *                     description="CSV file to import"
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function import(Request $request, string $countryCode, string $languageCode)
    {
        try{
            $fileType = $request->get('fileType');
            $this->validate($request, [
                'file' => ['file', 'mimetypes:text/plain,text/csv,application/vnd.ms-excel', 'max:2048'],
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





        $file = $request->file($fileType);
        try {
            $importer->importFile($file, $countryCode, $languageCode);
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

    
    /**
     * @OA\Get(
     *     path="/template/{country_code}",
     *     tags={"WhatNowImport"},
     *     summary="Export a blank CSV template",
     *     description="Exports a blank CSV template for a given country",
     *     operationId="exportBlankCsvTemplate",
     *     @OA\Parameter(
     *         name="country_code",
     *         in="path",
     *         required=true,
     *         description="Country code for which the blank template is generated",
     *         @OA\Schema(type="string", example="USA")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function exportBlank(Request $request,string $countryCode)
    {
        $extension = $request->query('extension', 'csv');
        $exporter = new RcnExporter($this->client);
        $region = $request->query('region', 'National');
        if (strlen($countryCode) !== 3) {
            return new JsonResponse(['message' => 'Invalid country code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            return $exporter->buildTemplate([],$countryCode,$extension,$region);
        } catch (CannotInsertRecord $e) {
            return $this->respondWithError($e, $e->getMessage());
        }
    }

    

    /**
     * @OA\Get(
     *     path="/template/{country_code}/{language_code}",
     *     tags={"WhatNowImport"},
     *     summary="Export a CSV template",
     *     description="Exports a CSV template for a given country and language",
     *     operationId="exportCsvTemplate",
     *     @OA\Parameter(
     *         name="country_code",
     *         in="path",
     *         required=true,
     *         description="Country code for which the template is generated",
     *         @OA\Schema(type="string", example="USA")
     *     ),
     *     @OA\Parameter(
     *         name="language_code",
     *         in="path",
     *         required=true,
     *         description="Language code for the template",
     *         @OA\Schema(type="string", example="en")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    /**
     * @throws AuthorizationException
     */
    public function export(Request $request, string $countryCode, string $languageCode)
    {
        $extension = $request->query('extension', 'xlsx');
        $region = $request->query('region', 'National');
        if (strlen($countryCode) !== 3) {
            return new JsonResponse(['message' => 'Invalid country code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (strlen($languageCode) !== 2) {
            return new JsonResponse(['message' => 'Invalid language code'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->authorize('listDrafts', Instruction::class);

        $exporter = new RcnExporter($this->client);

        try {
            $instructionRows = $exporter->buildInstructionRows($countryCode, $languageCode,$region);
            return $exporter->buildTemplate($instructionRows->toArray(),$countryCode,$extension,$region);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        } catch (RcnExportException $e) {
            return $this->respondWithError($e, $e->getMessage());
        }

    }
}
