<?php

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportInvalidFileException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportWillOverwriteException;
use App\Classes\RcnApi\Importer\RcnExporter;
use App\Classes\RcnApi\Importer\RcnImporter;
use App\Classes\RcnApi\Resources\WhatNowResourceInterface;
use Illuminate\Support\Collection;
use League\Csv\Reader;
use League\Csv\Writer;

class RcnImportTest extends \Tests\TestCase
{
    private static function createFakeInstruction(string $savedDate = 'now', bool $published = false)
    {
        $createdAt = new DateTimeImmutable($savedDate);

        return  Mockery::spy(Instruction::createFromResponse([
            'id' => 1,
            'countryCode' => 'en',
            'eventType' => 'Hurricane',
            'regionName' => 'South',
            'attribution' => [
                'countryCode' => 'en',
                'name' =>'America',
                'url' => 'https://google.com',
                'imageUrl' => 'https://google.com',
                'translations' => null
            ],
            'translations' => [
                'en' => [
                    'id' => 20,
                    'lang' => 'en',
                    'webUrl' => '',
                    'title' => 'title',
                    'description' => 'description',
                    'stages' => [],
                    'createdAt' => $createdAt->format('Y-m-d H:i:s'),
                    'published' => $published
                ]
            ]
        ]));
    }

    /**
     * @param string $exported
     * @param bool $blank
     * @return \League\Csv\AbstractCsv|static
     */
    private static function createFakeCsv(string $exported = 'now', $blank = false)
    {
        $exportedDate = new DateTimeImmutable($exported);
        $attribution = ['Key Messages', 'These are Key Messages', 'https://3sidedcube.com'];

        if ($blank === false) {
            $csv = RcnExporter::buildCsvTemplate('USA', new Collection([
                [
                    'Hurricane',
                    'No',
                    'South',
                    'Key Messages for Hurricane Event',
                    'Take Action',
                    'https://google.com',
                    'mitigation',
                    'seasonalForecast',
                    'warning',
                    'watch',
                    'immediate',
                    'recover'
                ]
            ]), $attribution, $exportedDate);
        } else {
            $csv = RcnExporter::buildCsvTemplate('USA', new Collection([]), $attribution, $exportedDate);
        }

        $reader = Reader::createFromString($csv->getContent());
        $reader->setHeaderOffset(RcnImporter::INSTRUCTION_HEADERS_OFFSET);
        return $reader;
    }

    public function test_import_warns_of_potential_overwrite_when_warn_flag_is_true()
    {
        $this->expectException(RcnImportWillOverwriteException::class);

        $client = Mockery::mock(WhatNowResourceInterface::class);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([
            self::createFakeInstruction('today') // api record has been updated recently
        ]));

        $csv = self::createFakeCsv('yesterday'); // csv was exported before update happened
        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'USA', 'en');

        $client->shouldNotHaveReceived('createInstruction');
        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_does_not_warn_of_potential_overwrite_when_warn_flag_is_false()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([
            self::createFakeInstruction('today') // api record has been updated recently
        ]));

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $client->shouldNotReceive('createInstruction');
        $client->shouldNotReceive('updateInstruction');

        $csv = self::createFakeCsv('yesterday'); // csv was exported before update happened

        $importer = new RcnImporter($client);
        $importer->turnWarningsOff(); // do not warn that an overwrite will happen
        $importer->importCsv($csv, 'USA', 'en');

        $report = $importer->getReport();
        $this->assertEquals("skipped", $report['importSummary'][0]['importAction']);

        Mockery::close();
    }

    public function test_import_overwrites_when_warn_flag_is_false_and_overwrite_is_true()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([
            self::createFakeInstruction('today') // api record has been updated recently
        ]));

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $client->shouldReceive('updateInstruction')->once()->andReturn(
            self::createFakeInstruction('now')
        );

        $csv = self::createFakeCsv('yesterday'); // csv was exported before update happened

        $importer = new RcnImporter($client);
        $importer->turnWarningsOff(); // do not warn that an overwrite will happen
        $importer->turnOverwritingOn(); // force update
        $importer->importCsv($csv, 'USA', 'en');

        $report = $importer->getReport();
        $this->assertEquals("updated", $report['importSummary'][0]['importAction']);

        $client->shouldNotHaveReceived('createInstruction');

        Mockery::close();
    }

    public function test_import_with_warnings_on_will_update_when_import_record_is_newer()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([
            self::createFakeInstruction('-1 week') // api record was updated long ago
        ]));

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $client->shouldReceive('updateInstruction')->once();

        $csv = self::createFakeCsv('yesterday');

        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'USA', 'en');

        $report = $importer->getReport();
        $this->assertEquals("updated", $report['importSummary'][0]['importAction']);

        $client->shouldNotHaveReceived('createInstruction');

        Mockery::close();
    }

    public function test_import_creates_new_instruction_when_record_does_not_exist()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andThrow(new RcnApiResourceNotFoundException());

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $client->shouldReceive('createInstruction')->once();

        $csv = self::createFakeCsv('yesterday');

        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'USA', 'en');

        $report = $importer->getReport();
        $this->assertEquals("added", $report['importSummary'][0]['importAction']);

        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_creates_new_translation_for_existing_instruction()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $instruction = self::createFakeInstruction();

        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([$instruction]));
        $client->shouldReceive('updateInstruction')->once();

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $csv = self::createFakeCsv('yesterday');

        $importer = new RcnImporter($client);
        $importer->turnWarningsOff(); // do not warn that an overwrite will happen
        $importer->importCsv($csv, 'USA', 'fr');

        $report = $importer->getReport();
        $this->assertEquals("updated", $report['importSummary'][0]['importAction']);

        $client->shouldNotHaveReceived('createInstruction');

        $instruction->shouldHaveReceived('getTranslationsByLanguage')->once();
        $instruction->shouldHaveReceived('setTranslation')->once();

        Mockery::close();
    }

    public function test_import_with_no_instructions_does_nothing()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $instruction = self::createFakeInstruction();

        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([$instruction]));

        $client->shouldReceive('getOrganisationByCountryCode')->andReturn(null);

        $csv = self::createFakeCsv('yesterday', true);

        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'USA', 'en');

        $report = $importer->getReport();
        $this->assertCount(0, $report['importSummary']);

        $client->shouldNotHaveReceived('createInstruction');
        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_with_csv_for_wrong_country_code()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $this->expectException(RcnImportInvalidFileException::class);

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $csv = self::createFakeCsv('yesterday', true);

        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'CAN', 'en');

        $client->shouldNotHaveReceived('createInstruction');
        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_with_malformed_country_code()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $this->expectException(RcnImportInvalidFileException::class);

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $csv = self::createFakeCsv('yesterday', true);

        $importer = new RcnImporter($client);
        $importer->importCsv($csv, 'HELLO', 'en');

        $client->shouldNotHaveReceived('createInstruction');
        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_with_messed_up_columns()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $this->expectException(RcnImportInvalidFileException::class);

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $instruction = self::createFakeInstruction();

        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([$instruction]));

        $exportedDate = new \DateTimeImmutable('now');

        $csv = Writer::createFromString('');

        $encodedDate = base64_encode($exportedDate->format('c'));

        // https://www.w3.org/TR/tabular-data-model/#embedded-metadata
        $csv->insertOne([sprintf(
            '#%s %s %s',
            'USA',
            trans('csvTemplate.separator'),
            $encodedDate
        )]);

        // Likely scenario: more columns added for the translation.
        $csv->insertOne(['#'.trans('csvTemplate.help'), null, null, null, null, trans('csvTemplate.stagesInstruction')]);
        $csv->insertOne([
            trans('csvTemplate.instruction_columns.eventType'),
            trans('csvTemplate.instruction_columns.regionName'),
            trans('csvTemplate.instruction_columns.title'),
            trans('csvTemplate.instruction_columns.description'),
            trans('csvTemplate.instruction_columns.webUrl'),
            'mitigation En',
            'mitigation Fr',
            'seasonalForecast En',
            'seasonalForecast Fr',
            'warning En',
            'warning Fr',
            'watch En',
            'watch Fr',
            'immediate En',
            'immediate Fr',
            'recover En',
            'recover Fr'
        ]);
        $csv->insertAll(new Collection([[
            'Hurricane',
            'South',
            'Key Messages for Hurricane Event',
            'Take Action',
            'https://google.com',
            'mitigation',
            'le mitigation',
            'seasonalForecast',
            'le seasonalForecast',
            'warning',
            'le warning',
            'watch',
            'le watch',
            'immediate',
            'le immediate',
            'recover',
            'le recover'
        ]]));

        $reader = Reader::createFromString($csv->getContent());
        $reader->setHeaderOffset(RcnImporter::ATTRIBUTION_HEADERS_OFFSET);

        $importer = new RcnImporter($client);
        $importer->importCsv($reader, 'USA', 'fr');

        $client->shouldNotHaveReceived('createInstruction');
        $client->shouldNotHaveReceived('updateInstruction');

        Mockery::close();
    }

    public function test_import_attribution_import_with_valid_data()
    {
        $adminUser = factory(\App\Models\Access\User\User::class)->create();
        $adminUser->userProfile()->save(factory(\App\Models\Access\User\UserProfile::class)->make());
        $role = App\Models\Access\Role\Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                \App\Models\Access\Permission\Permission::where('name', '=', 'view-backend')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser);

        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $organisation = Organisation::createFromArray([
            'countryCode' => 'USA',
            'name' => 'American Red Cross',
            'url' => 'https://test.com',
            'translations' => []
        ]);

        $client->shouldReceive('getOrganisationByCountryCode')->once()->andReturn($organisation);
        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([]));
        $client->shouldReceive('updateOrganisationByCountryCode')->once();

        $exportedDate = new DateTimeImmutable('now');
        $attribution = ['Key Messages', 'These are Key Messages', 'https://3sidedcube.com'];
        $csv = RcnExporter::buildCsvTemplate('USA', new Collection([]), $attribution, $exportedDate);
        $reader = Reader::createFromString($csv->getContent());

        $importer = new RcnImporter($client);
        $importer->importCsv($reader, 'USA', 'en');

        Mockery::close();
    }

    public function test_import_attribution_import_rejects_bad_url()
    {
        $this->withoutEvents(); // stop history from being written as we don't have a logged in user

        $this->expectException(RcnImportInvalidFileException::class);

        $client = Mockery::mock(WhatNowResourceInterface::class);

        $organisation = Organisation::createFromArray([
            'countryCode' => 'USA',
            'name' => 'American Red Cross',
            'url' => 'https://test.com',
            'translations' => []
        ]);

        $client->shouldReceive('getLatestInstructionsByCountryCode')->andReturn(new Collection([]));
        $client->shouldReceive('updateOrganisationByCountryCode')->never();

        $exportedDate = new DateTimeImmutable('now');
        $attribution = ['Key Messages', 'These are Key Messages', 'www.3sidedcube.com'];
        $csv = RcnExporter::buildCsvTemplate('USA', new Collection([]), $attribution, $exportedDate);
        $reader = Reader::createFromString($csv->getContent());

        $importer = new RcnImporter($client);
        $importer->importCsv($reader, 'USA', 'en');

        Mockery::close();
    }
}
