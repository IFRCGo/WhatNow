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
use Maatwebsite\Excel\Facades\Excel;
class RcnImporter
{
    public const ATTRIBUTION_HEADERS_OFFSET = 2;
    public const INSTRUCTION_HEADERS_OFFSET = 5;

    public const ERROR_CODES = [
        'INVALID_COLUMN_HEADINGS' => 30001,
        'MISSING_ATTRIBUTION' => 30002,
        'INVALID_URL' => 30003,
    ];

    private $bulkUpload;
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
    private $turnRejectOverwritingOn = true;


    public function __construct(WhatNowResourceInterface $client)
    {
        $this->client = $client;
        $this->bulkUpload = new BulkUploadTemplateImport();
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


    /**
     * @throws RcnImportException
     */
    public function importFile($file, string $countryCode, string $languageCode)
    {
        $organisation = $this->client->getOrganisationByCountryCode($countryCode);
        if (! $organisation) {
            throw new RcnApiResourceNotFoundException("Organisation with this country code '{$countryCode}' not found");
        }
        Excel::import($this->bulkUpload,$file);
        if($organisation->getName() !== $this->bulkUpload->getData()['nationalSociety']) throw new RcnImportException("The national society selected is not equal to the national society in the file");
        $this->metadata = new ImportMetadata($countryCode, $languageCode,$this->bulkUpload->getData()['region']);
        $this->runInstructionsImport($countryCode,$this->bulkUpload->getData());
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


    /**
     * @throws RcnImportException
     * @throws RcnImportWillOverwriteException
     */
    private function runInstructionsImport(string $countryCode, Array $records)
    {
        try {
            $existingInstructions = $this->client->getLatestInstructionsByCountryCode($countryCode);
        } catch (RcnApiResourceNotFoundException $e) {
            $existingInstructions = new Collection;
        } catch (RcnApiException $e) {
            throw new RcnImportException();
        }

        $this->checkForPotentialOverwrites($records, $existingInstructions);
        foreach ($records['hazards'] as $record) {
            $existingInstruction = self::findExisting(
                $existingInstructions,
                $record['hazard'],
                $this->metadata->getRegion()
            );
            $this->importInstructionRecord($record, $existingInstruction);
        }


    }

    /**
     * @throws RcnImportWillOverwriteException
     */
    private function checkForPotentialOverwrites(array $records, $existingInstructions)
    {
        if (! $this->canWarn()) {
            return;
        }
        foreach ($records['hazards'] as $offset => $hazard) {
            $existingInstruction = $this->findExisting(
                $existingInstructions,
                $hazard['hazard'],
                $records['region']
            );

            if ($existingInstruction) {
                $existingTranslation = $existingInstruction->getTranslationsByLanguage($this->metadata->getLanguageCode());
                if ($existingTranslation){
                    throw new RcnImportWillOverwriteException('Import will overwrite '.$existingTranslation->getId());
                }

            }
        }

    }


    private function importInstructionRecord($record, Instruction $existingInstruction = null)
    {
        $stages = [];
        foreach ($record['urgencyLevels'] as $stage) { // Parsing stages as the Instruction model requires
                foreach ($stage['safetyMessages'] as $safetyMessage) {
                    $stages[strtolower(str_replace(" ",'_',$stage['urgencyLevel']))][] =
                        [
                            'title' => $safetyMessage['safetyMessage'],
                            'content' => $safetyMessage['supportingMessages'],
                        ]
                    ;
                }
        }
        $translationRequest = [
            'lang' => $this->metadata->getLanguageCode(),
            'webUrl' => $record['url'] ?: null,
            'regionName' => ($this->metadata->getRegion()) ?: null,
            'title' => ($record['title']) ?: null,
            'description' => ($record['description']) ?: null,
            'stages' => $stages,
        ];

        if (! $existingInstruction instanceof Instruction) {
            $newInstruction = Instruction::createFromRequest([
                'countryCode' => $this->metadata->getCountryCode(),
                'eventType' => $record['hazard'],
                'regionName' => $this->metadata->getRegion(),
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

        if ($existingTranslation) {
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

        if (count($changes) > 0 || ! $existingTranslation ) {
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
