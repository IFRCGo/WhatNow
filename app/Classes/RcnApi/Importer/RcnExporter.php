<?php

namespace App\Classes\RcnApi\Importer;

use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Importer\Exceptions\RcnExportException;
use App\Classes\RcnApi\Resources\WhatNowResourceInterface;
use App\Models\EventType;
use Illuminate\Support\Collection;
use League\Csv\Writer;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;

class RcnExporter
{

    private $client;


    public function __construct(WhatNowResourceInterface $client)
    {
        $this->client = $client;
    }


    public function buildInstructionRows(string $countryCode, string $languageCode,string $region = 'National'): Collection
    {
        $instructions = $this->client->getLatestInstructionsByCountryCode($countryCode);

        $eventTypesOnDB = EventType::all();

        $rows = [];

        foreach ($instructions as $instruction) {
                if($region != "" and $instruction->getRegionName() != $region) continue;

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
                            $row[] = isset($stages[$label]) && is_array($stages[$label]) && isset($stages[$label][$i]) ? array_merge($stages[$label][$i],['stage' => $label]) : '';
                        }
                        $rows[] = $row;
                    } else{
                        $row = array_fill(0, 6, '');
                        foreach ($stageLabels as $label) {
                            $row[] = isset($stages[$label]) && is_array($stages[$label]) && isset($stages[$label][$i]) ? array_merge($stages[$label][$i],['stage' => $label]) : '';
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
    ): Writer
    {
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
        string $format = 'xlsx',
        string $regionName = 'National'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {

        // Retrieve organization data using country code
        $organisation = $this->client->getOrganisationByCountryCode($countryCode);

        // Define the output file name
        $fileName = "template.$format";

        // Initialize arrays to store formatted data
        $data = [];
        $hazardAdded = []; // Main array to collect all the information
        $hazard = '';
        $maxSupportingMessages = 0;
        foreach ($items as $row) {
            // Extract Title, Description, URL, and Hazard from row
            $rowToInsert = [$row[3] ?? '', $row[4]?? '', $row[5] ?? '', $row[0] ?? ''];

            // Assign hazard only if it is not empty and has not been added before
            if ($row[0] != '' && !in_array($row[0], $hazardAdded)) {
                $hazard = $row[0];
            }

            // Iterate through urgency levels (stages) from index 6 to 11
            for ($i = 6; $i < 12; $i++) {
                if (!isset($row[$i]) || $row[$i] === '') {
                    continue; // Skip empty stage data
                }

                $stage = ucfirst($row[$i]['stage'] ?? ''); // Extract stage name and capitalize
                if ($stage === '') {
                    continue; // Skip if stage is empty
                }

                // Check if the stage already exists within the hazard to prevent duplication
                $stageFound = isset($hazardAdded[$hazard][$stage]);

                // Ensure title, description, and URL are not duplicated
                $rowToInsertFormatted = isset($hazardAdded[$hazard])
                    ? ['', '', '', ''] // Empty values to prevent repetition
                    : $rowToInsert;

                // Include stage only if it has not been added before
                $rowToInsertFormatted[] = $stageFound ? '' : str_replace("_"," ",$stage);

                // Add the stage title
                $rowToInsertFormatted[] = $row[$i]['title'] ?? '';

                // Append safety messages
                $maxSupportingMessages = max(count($row[$i]['content']), $maxSupportingMessages);
                foreach ($row[$i]['content'] as $safetyMessage) {
                    $rowToInsertFormatted[] = $safetyMessage;
                }

                // Store processed data within the hazard category
                $hazardAdded[$hazard][$stage][] = $rowToInsertFormatted;
            }
        }

        // Flatten the structured data for final export
        foreach ($hazardAdded as $stages) {
            foreach ($stages as $stageValues) {
                $data = array_merge($data, $stageValues);
            }
        }

        // Generate and return the Excel file
        return Excel::download(
            new BulkUploadTemplateExport($organisation->getName(), $regionName, $data,$maxSupportingMessages),
            $fileName,
            $format === "csv" ? ExcelFormat::CSV : ExcelFormat::XLSX
        );
    }



    public static function createAttributionHeader(): array
    {
        return [
            trans('csvTemplate.attribution_columns.name'),
            trans('csvTemplate.attribution_columns.message'),
            trans('csvTemplate.attribution_columns.url'),
        ];
    }


    public static function createInstructionsHeader(): array
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


    public function buildAttributionRow(string $countryCode, string $languageCode): array
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
