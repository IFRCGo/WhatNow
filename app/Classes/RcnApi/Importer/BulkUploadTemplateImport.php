<?php

namespace App\Classes\RcnApi\Importer;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportException;
class BulkUploadTemplateImport implements ToCollection
{

    private $data = [];
    private $dataPageProcessed = false;

    /**
     * Process the imported data.
     *
     * @param ToCollection $rows
     * @return array
     * @throws RcnImportException
     */
    public function collection(Collection $rows): array
    {
        if($this->dataPageProcessed) return [];

        $processedData = [
            'nationalSociety' => null,
            'region' => null,
            'hazards' => []
        ];

        $currentHazard = null;
        $currentUrgencyLevel = null;
        $firstRow = false;

        foreach ($rows as $index => $row) {
            if (!$firstRow || $index === 2) {
                $firstRow = true;
                continue;
            };
            if ($index === 1) {
                if(!$row[0]){
                    throw new RcnImportException("Missing national society");
                }
                $this->dataPageProcessed  = true;
                $processedData['nationalSociety'] = $row[0];
                $processedData['region'] = $row[1] ?? 'National';
                continue;
            }
            // Handle hazards and messages
            if (!empty($row[3])) {
                if(!$row[0] || !$row[1]){
                    throw new RcnImportException("Title, description are missing or invalid");
                }
                $currentHazard = [
                    'title' => $row[0],
                    'description' => $row[1],
                    'url' => $row[2],
                    'hazard' => $row[3],
                    'urgencyLevels' => [],
                ];
            }

            if (!empty($row[4])) {
                $currentUrgencyLevel = [
                    'urgencyLevel' => $row[4],
                    'safetyMessages' => []
                ];
            }

            if (!empty($row[5])) {
                $currentUrgencyLevel['safetyMessages'][] = [
                    'safetyMessage' => $row[5],
                    'supportingMessages' => []
                ];
            }

            $supportingMessages = [];
            for ($i = 6; $i < count($row); $i++) {
                if (!empty($row[$i])) {
                    $supportingMessages[] = $row[$i];
                }
            }
            if(count($supportingMessages) > 0){
                $lastIndex = array_key_last($currentUrgencyLevel['safetyMessages']);
                $currentUrgencyLevel['safetyMessages'][$lastIndex]['supportingMessages'] = $supportingMessages;
            }



            if ($currentUrgencyLevel) {
                $existingHazardIndex = null;

                foreach ($processedData['hazards'] as $pos => $hazardEntry) {
                    if ($hazardEntry['hazard'] === $currentHazard['hazard']) {
                        $existingHazardIndex = $pos;
                        break;
                    }
                }
                $existingUrgencyLevel = false;
                if ($existingHazardIndex !== null) {

                    foreach ($processedData['hazards'][$existingHazardIndex]['urgencyLevels'] as $pos => $urgencyLevelEntry) {
                        if($urgencyLevelEntry['urgencyLevel'] === $currentUrgencyLevel['urgencyLevel']){
                            $processedData['hazards'][$existingHazardIndex]['urgencyLevels'][$pos]['safetyMessages'] = $currentUrgencyLevel['safetyMessages'];
                            $existingUrgencyLevel = true;
                            break;
                        }
                    }
                    if(!$existingUrgencyLevel){
                        $processedData['hazards'][$existingHazardIndex]['urgencyLevels'][] = $currentUrgencyLevel;
                    }
                } else {
                    $currentHazard['urgencyLevels'][] = $currentUrgencyLevel;
                    $processedData['hazards'][] = $currentHazard;
                }
            }
        }
        $this->data = $processedData;
        return $processedData;
    }
    public function getData()
    {
        return $this->data;
    }



}

