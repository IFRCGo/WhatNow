<?php

namespace App\Classes\RcnApi\Importer;

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\InstructionTranslation;
use App\Classes\RcnApi\Entities\OrganisationTranslation;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportInvalidFileException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportWillOverwriteException;
use App\Classes\RcnApi\Resources\WhatNowResourceInterface;
use App\Events\WhatNow\InstructionCreatedViaImport;
use App\Events\WhatNow\InstructionUpdatedViaImport;
use App\Models\Access\User\User;
use Illuminate\Support\Collection;
use League\Csv\MapIterator;
use League\Csv\Reader;

class RcnImporter
{
    public const ATTRIBUTION_HEADERS_OFFSET = 2;
    public const INSTRUCTION_HEADERS_OFFSET = 5;

    public const ERROR_CODES = [
        'INVALID_COLUMN_HEADINGS' => 30001,
        'MISSING_ATTRIBUTION' => 30002,
        'INVALID_URL' => 30003,
    ];

    
    private $client;

    
    private $metadata;

    
    private $warnings = true;

    
    private $overwrite = false;

    
    private $created = [];

    
    private $updated = [];

    
    private $skipped = [];

    
    private $failed = [];

    
    private $invalid = [];

    
    private $attributionStatus = 'skipped';

    private $current = null;

    private $previous = null;

    private $stages = [];

    private $info = [
        'society' => '',
        'language' => '',
        'importSummary' => [],
    ];

    
    public function __construct(WhatNowResourceInterface $client)
    {
        $this->client = $client;
    }

    
    public function getReport()
    {
        $this->mapInfoEvent($this->created, 'added');
        $this->mapInfoEvent($this->updated, 'updated');
        $this->mapInfoEvent($this->skipped, 'skipped');
        $this->mapInfoEvent($this->failed, 'failed');

        return $this->info;
    }

    private function mapInfoEvent($instructions, $action)
    {
        foreach ($instructions as $instruction) {
            $data = [
                'eventName' => $instruction->getEventType(),
                'importAction' => $action,
                'stages' => data_get($instruction, 'changes', []),
            ];

            $this->info['importSummary'][] = $data;
        }
    }

    
    public function canOverwrite(): bool
    {
        return $this->overwrite;
    }

    public function canWarn(): bool
    {
        return $this->warnings;
    }

    public function turnWarningsOff()
    {
        $this->warnings = false;
    }

    public function turnOverwritingOn()
    {
        $this->overwrite = true;
    }

    
    public function importCsv(Reader $csv, string $countryCode, string $languageCode)
    {
        $metadataLine = $csv->fetchOne(0);         $this->metadata = new ImportMetadata($metadataLine, $countryCode, $languageCode);
        $this->info['language'] = $languageCode;

        $csv->setHeaderOffset(self::INSTRUCTION_HEADERS_OFFSET);
        if (RcnExporter::createInstructionsHeader() !== $csv->getHeader()) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_COLUMN_HEADINGS'],
                'Column headings do not match'
            );
        }

        $csv->setHeaderOffset(self::ATTRIBUTION_HEADERS_OFFSET);
        if (RcnExporter::createAttributionHeader() !== array_slice($csv->getHeader(), 0, 3)) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_COLUMN_HEADINGS'],
                'Column headings do not match'
            );
        }

        $csv->setHeaderOffset(self::INSTRUCTION_HEADERS_OFFSET);

                                $attributionData = array_filter(array_values($csv->fetchOne(3)));

        $name = isset($attributionData[0]) ? $attributionData[0] : null;
        $message = isset($attributionData[1]) ? $attributionData[1] : null;
        $url = isset($attributionData[2]) ? $attributionData[2] : null;

        if (! is_null($url) && ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_URL'],
                'Column headings do not match'
            );
        }

        $this->runInstructionsImport($csv->getRecords());
        $this->saveAttributionData($languageCode, $countryCode, $name, $message, $url);
    }

    
    private function saveAttributionData(string $languageCode, string $countryCode, string $name = null, string $message = null, string $url = null)
    {
        if (! $name || ! $message) {
            return;
        }

        $organisation = $this->client->getOrganisationByCountryCode($countryCode);
        if (! $organisation) {
            return;
        }

        $this->info['society'] = $organisation->getName();
        $existingTranslation = $organisation->getTranslationsByLanguage($languageCode);


        
        $user = auth()->user();

        if ($existingTranslation && $existingTranslation->isPublished() && ! $user->hasPermission('content-publish')) {
            $this->attributionStatus = 'denied';

            return;
        }

        $organisationTranslation = OrganisationTranslation::createFromResponse([
            'name' => $name,
            'attributionMessage' => $message,
            'languageCode' => $languageCode,
            'published' => ($existingTranslation) ? $existingTranslation->isPublished() : false,
        ]);

        $organisation->setTranslation($organisationTranslation);

        if ($url && $user->hasPermission('content-publish')) {
            $organisation->setUrl($url);
        }

        try {
            $this->client->updateOrganisationByCountryCode($organisation);
            $this->attributionStatus = 'success';
        } catch (RcnApiException $e) {
            throw new RcnImportException();
        }
    }

    
    private function runInstructionsImport(MapIterator $records)
    {
        try {
            $existingInstructions = $this->client->getLatestInstructionsByCountryCode($this->metadata->getCountryCode());
        } catch (RcnApiResourceNotFoundException $e) {
            $existingInstructions = new Collection;
        } catch (RcnApiException $e) {
            throw new RcnImportException();
        }

        $validRecords = $this->prepareValidRecords($records);

                $validRecords = $validRecords->map(function ($record) {
            unset($record[trans('csvTemplate.instruction_columns.otherType')]);

            return $record;
        });

        $this->checkForPotentialOverwrites($validRecords, $existingInstructions);

        $validRecords->each(function (array $record) use ($existingInstructions) {
            $existingInstruction = self::findExisting(
                $existingInstructions,
                $record[trans('csvTemplate.instruction_columns.eventType')],
                $record[trans('csvTemplate.instruction_columns.regionName')]
            );

            $this->importInstructionRecord($record, $existingInstruction);
        });
    }

    private function prepareValidRecords($records): Collection
    {
        $validRecords = new Collection();
        $fieldEventType = trans('csvTemplate.instruction_columns.eventType');
        $stages = array_fill_keys(InstructionTranslation::EVENT_STAGES, null);
        $needsAdding = false;

        foreach ($records as $offset => $record) {
            if ($offset < self::INSTRUCTION_HEADERS_OFFSET) {
                continue;             }

            if (empty($this->current) && empty($record[$fieldEventType])) {
                $this->invalid[] = $offset + 1;

                continue;             }

            if (! empty($record[$fieldEventType])) {
                $this->current = $record;
            }

            if (empty($this->previous[$fieldEventType])) {
                $this->previous = $this->current;
            }

            if ($this->current[$fieldEventType] != $this->previous[$fieldEventType]) {
                $needsAdding = false;
                foreach ($this->stages as $key => $value) {
                    $this->previous[$key] = implode(trans('csvTemplate.separator'), $value);
                }
                $validRecords->push($this->previous);
                $this->stages = [];
                $this->previous = $this->current;
                foreach ($stages as $stage => $null) {
                    $key = trans('csvTemplate.instruction_columns.stages.'.$stage);
                    if (! empty($record[$key])) {
                        $this->stages[$key][] = $record[$key] ;
                    }
                }
            } else {
                $needsAdding = true;
                foreach ($stages as $stage => $null) {
                    $key = trans('csvTemplate.instruction_columns.stages.'.$stage);
                    if (! empty($record[$key])) {
                        $this->stages[$key][] = $record[$key] ;
                    }
                }
            }
        }

        if ($needsAdding) {
            foreach ($this->stages as $key => $value) {
                $this->previous[$key] = implode(trans('csvTemplate.separator'), $value);
            }
            $validRecords->push($this->previous);
            $this->stages = [];
            $this->previous = $this->current;
        }

        return $validRecords;
    }

    
    private function checkForPotentialOverwrites(Collection $records, $existingInstructions)
    {
        if (! $this->canWarn()) {
            return;
        }

        $records->each(function (array $record) use ($existingInstructions) {
            $existingInstruction = self::findExisting(
                $existingInstructions,
                $record[trans('csvTemplate.instruction_columns.eventType')],
                $record[trans('csvTemplate.instruction_columns.regionName')]
            );

            if ($existingInstruction) {
                $existingTranslation = $existingInstruction->getTranslationsByLanguage($this->metadata->getLanguageCode());
                if ($existingTranslation && $existingTranslation->getCreatedAt() > $this->metadata->getExportDate()
                ) {
                    throw new RcnImportWillOverwriteException('Import will overwrite '.$existingTranslation->getId());
                }
            }
        });
    }

    
    private function importInstructionRecord($record, Instruction $existingInstruction = null)
    {
        $stages = array_fill_keys(InstructionTranslation::EVENT_STAGES, null);
        foreach ($stages as $stage => $null) {
            $stageKey = trans('csvTemplate.instruction_columns.stages.'.$stage);
            if (! isset($record[$stageKey])) {
                continue;             }

            $stageString = ($record[$stageKey]) ?: null;
            if (is_null($stageString)) {
                $stages[$stage] = null; 
                continue;
            }

            $stageArray = explode(trans('csvTemplate.separator'), $stageString);
            $stageArray = array_values(array_map('trim', $stageArray));

            $stages[$stage] = $stageArray;
        }

        $translationRequest = [
            'lang' => $this->metadata->getLanguageCode(),
            'webUrl' => ($record[trans('csvTemplate.instruction_columns.webUrl')]) ?: null,
            'regionName' => ($record[trans('csvTemplate.instruction_columns.regionName')]) ?: null,
            'title' => ($record[trans('csvTemplate.instruction_columns.title')]) ?: null,
            'description' => ($record[trans('csvTemplate.instruction_columns.description')]) ?: null,
            'stages' => $stages,
        ];

        if (! $existingInstruction instanceof Instruction) {
            $newInstruction = Instruction::createFromRequest([
                'countryCode' => $this->metadata->getCountryCode(),
                'eventType' => $record[trans('csvTemplate.instruction_columns.eventType')],
                'regionName' => $record[trans('csvTemplate.instruction_columns.regionName')],
                'translations' => [
                    $this->metadata->getCountryCode() => $translationRequest,
                ],
            ]);

            try {
                $this->client->createInstruction($newInstruction);
                event(new InstructionCreatedViaImport($newInstruction));
                $this->created[] = $newInstruction;
            } catch (RcnApiException $e) {
                $this->failed[] = $newInstruction;
            }

            return;
        }

        $existingTranslation = $existingInstruction->getTranslationsByLanguage($this->metadata->getLanguageCode());
        $force = false;

        if ($existingTranslation
            && $existingTranslation->getCreatedAt() > $this->metadata->getExportDate()
        ) {
            if (! $this->canOverwrite()) {
                                $this->skipped[] = $existingInstruction;

                return;
            }

            $force = true;
        }

        $original = json_encode($existingInstruction);
        $existingInstruction->setTranslation(InstructionTranslation::createFromRequest($translationRequest));
        $existingInstruction->setRegionName($translationRequest['regionName']);
        $changes = $this->whatsDifferent($original, json_encode($existingInstruction));

        if (count($changes) > 0 || ! $existingTranslation) {
            try {
                $r = $this->client->updateInstruction($existingInstruction);

                event(new InstructionUpdatedViaImport($existingInstruction, $force));
                $existingInstruction->changes = $changes;
                $this->updated[] = $existingInstruction;
            } catch (RcnApiException $e) {
                $this->failed[] = $existingInstruction;
            }
        } else {
            $this->skipped[] = $existingInstruction;
        }
    }

    private function whatsDifferent(string $old, string $new): array
    {
        $originalInstruction = json_decode($old);
        $updatedInstruction = json_decode($new);
        $original = (array) $originalInstruction->translations;
        $updated = (array) $updatedInstruction->translations;

        $transProps = ['title', 'description', 'webUrl'];
        $stages = array_fill_keys(InstructionTranslation::EVENT_STAGES, null);

        $changes = [];

        if (data_get($updatedInstruction, 'regionName', '') != data_get($originalInstruction, 'regionName', '')) {
            $changes[] = 'regionName';
        }

        foreach ($original  as $transKey => $value) {
            foreach ($transProps as $key) {
                $u = data_get($updated[$transKey], $key);
                $o = data_get($original[$transKey], $key);
                if (empty($u) != empty($o) || $u != $o) {
                    $changes[] = $key;
                }
            }

            $uStages = (array) data_get($updated[$transKey], 'stages', []);
            $oStages = (array) data_get($original[$transKey], 'stages', []);

            foreach (array_keys($stages) as $stage) {
                $oStage = data_get($oStages, $stage);
                $uStage = data_get($uStages, $stage);
                if (json_encode($oStage) !== json_encode($uStage)) {
                    $changes[] = $stage;
                }
            }
        }

        return $changes;
    }

    
    private static function findExisting(Collection $existingInstructions, string $eventType, string $regionName): ?Instruction
    {
        $region = empty($regionName) ? 'National' : $regionName;

        return $existingInstructions->first(function (Instruction $instruction) use ($eventType, $region) {
            return $instruction->getEventType() === $eventType && $instruction->getRegionName() === $region;
        });
    }
}
