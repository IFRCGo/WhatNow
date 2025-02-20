<?php

namespace App\Classes\RcnApi\Importer;

use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Importer\Exceptions\RcnExportException;
use App\Classes\RcnApi\Resources\WhatNowResourceInterface;
use App\Models\EventType;
use Illuminate\Support\Collection;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
class RcnExporter
{

    private $client;


    public function __construct(WhatNowResourceInterface $client)
    {
        $this->client = $client;
    }


    public function buildInstructionRows(string $countryCode, string $languageCode)
    {
        $instructions = $this->client->getLatestInstructionsByCountryCode($countryCode);

        $eventTypesOnDB = EventType::all();

        $rows = [];

        foreach ($instructions as $instruction) {

                        $matchingTranslations = array_filter(
                $instruction->getTranslations(),
                function (InstructionTranslation $translation) use ($languageCode) {
                    return $translation->getLang() === $languageCode;
                }
            );

                        if (count($matchingTranslations) > 1) {
                throw new RcnExportException('The current data for this translation is malformed');
            }


            $translation = array_first($matchingTranslations);

            $row = [
                $instruction->getEventType(),
                is_null($eventTypesOnDB->where('name', $instruction->getEventType())->first()) ? 'Yes' : 'No',
               $instruction->getRegionName(),
            ];

            if (!$translation) {
                $rows[] = $row;
                continue;
            }

            $row = array_merge($row, [
                $translation->getTitle(),
                $translation->getDescription(),
                $translation->getWebUrl()
            ]);

            if ($translation->getStages()->isNotEmpty()) {
                $stages =  $translation->getStages()->toArray();
                $stageRows = $this->calcStageRowCount($stages);
                $stageLabels = $this->getStageKeys($stages);

                for($i = 0 ; $i < $stageRows ;$i++){
                    if($i === 0) {
                        foreach ($stageLabels as $label) {
                            $row[] = isset($stages[$label]) && is_array($stages[$label]) && isset($stages[$label][$i]) ? $stages[$label][$i] : '';
                        }
                        $rows[] = $row;
                    } else{
                        $row = array_fill(0, 6, '');
                        foreach ($stageLabels as $label) {
                            $row[] = isset($stages[$label]) && is_array($stages[$label]) && isset($stages[$label][$i]) ? $stages[$label][$i] : '';
                        }
                        $rows[] = $row;
                    }
                }
            } else {
                $rows[] = $row;
            }
        }

        return collect($rows);
    }

    private function calcStageRowCount($stages): int
    {
        $max = 0;
        foreach ($stages as $stage){
            if(is_array($stage)){
                $max = count($stage) > $max ? count($stage) : $max;
            }
        }
        return $max;
    }

    private function getStageKeys($stages): array
    {
        $keys = [];
        foreach ($stages as $key=>$stage){
            $keys[] = $key;
        }
        return $keys;
    }


    public static function buildCsvTemplate(
        $countryCode,
        Collection $instructionRows = null,
        array $attributionRow = [],
        \DateTimeImmutable $exportedDate = null
    ) {
        if ($exportedDate === null) {
            $exportedDate = new \DateTimeImmutable('now');
        }

        $csv = Writer::createFromString('');

        $stamp = sprintf('%s%s', $countryCode, $exportedDate->format('c'));
        $encodedStamp = base64_encode($stamp);

                $csv->insertOne(['#'.$encodedStamp]);

        $csv->insertOne(['#'.trans('csvTemplate.attribution_heading')]);
        $csv->insertOne(self::createAttributionHeader());
        $csv->insertOne($attributionRow);

        $csv->insertOne(['#'.trans('csvTemplate.instructions_heading'), null, null, null, '#'.trans('csvTemplate.stagesInstruction')]);
        $csv->insertOne(self::createInstructionsHeader());

        if (!is_null($instructionRows)) {
            $csv->insertAll($instructionRows);
        }

        return $csv;
    }


    public function buildTemplate(
        array $items,
        string $countryCode,
        string $format = 'xlsx'

    ): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $organisation = $this->client->getOrganisationByCountryCode($countryCode);
        $fileName = "template.$format";
        $data = [
            ['Test Title 1', 'Description 1', 'https://example.com', 'Cyclone', 'High', 'Key Message 1', 'Support 1', 'Support 2', 'Support 3'],
        ];

        return Excel::download(new BulkUploadTemplateExport($organisation->getName(),'',$data ), $fileName, $format == "csv" ? ExcelFormat::CSV : ExcelFormat::XLSX);
    }


    public static function createAttributionHeader()
    {
        return [
            trans('csvTemplate.attribution_columns.name'),
            trans('csvTemplate.attribution_columns.message'),
            trans('csvTemplate.attribution_columns.url'),
        ];
    }


    public static function createInstructionsHeader()
    {
        return array_merge([
            trans('csvTemplate.instruction_columns.eventType'),
            trans('csvTemplate.instruction_columns.otherType'),
            trans('csvTemplate.instruction_columns.regionName'),
            trans('csvTemplate.instruction_columns.title'),
            trans('csvTemplate.instruction_columns.description'),
            trans('csvTemplate.instruction_columns.webUrl'),
        ], array_map(function ($stage) {
            return trans('csvTemplate.instruction_columns.stages.'.$stage);
        }, InstructionTranslation::EVENT_STAGES));
    }


    public function buildAttributionRow(string $countryCode, string $languageCode)
    {
        $organisation = $this->client->getOrganisationByCountryCode($countryCode);
        $attribution = $organisation->getTranslationsByLanguage($languageCode);

        if (!$attribution) {
            return [null, null, $organisation->getUrl()];
        }

        return [
            $attribution->getName(),
            $attribution->getAttributionMessage(),
            $organisation->getUrl()
        ];
    }
}
