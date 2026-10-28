<?php

use App\Classes\RcnApi\Entities\Instruction;
use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportException;
use App\Classes\RcnApi\Importer\Exceptions\RcnImportWillOverwriteException;
use App\Classes\RcnApi\Importer\RcnImporter;
use App\Classes\RcnApi\Resources\WhatNowResourceInterface;
use App\Events\WhatNow\InstructionCreatedViaImport;
use App\Events\WhatNow\InstructionUpdatedViaImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RcnImportTest extends \Tests\TestCase
{
    private $client;
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake([InstructionCreatedViaImport::class, InstructionUpdatedViaImport::class]);
        $this->client = Mockery::mock(WhatNowResourceInterface::class);
        $organisation = Organisation::createFromArray([
            'countryCode' => 'USA', 'name' => 'American Red Cross',
            'url' => 'https://example.test', 'imageUrl' => null, 'translations' => [],
        ]);
        $this->client->shouldReceive('getOrganisationByCountryCode')->with('USA')
            ->andReturn($organisation)->byDefault();
        $this->client->shouldReceive('getLatestInstructionsByCountryCode')->with('USA')
            ->andReturn(new Collection())->byDefault();
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
        parent::tearDown();
    }

    private function workbook(array $rows = [], string $society = 'American Red Cross', string $region = 'South'): string
    {
        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->fromArray(array_merge([
            ['National Society', 'Subnationals'],
            [$society, $region],
            ['Title', 'Description', 'URL', 'Hazard', 'Urgency Level', 'Safety Message', 'Supporting Message'],
        ], $rows), null, 'A1');
        $path = tempnam(sys_get_temp_dir(), 'whatnow-import-');
        unlink($path);
        $path .= '.xlsx';
        $this->files[] = $path;
        (new Xlsx($sheet))->save($path);
        $sheet->disconnectWorksheets();
        return $path;
    }

    private function hazardRow(): array
    {
        return ['Hurricane safety', 'Prepare for a hurricane', 'https://example.test/safety',
            'Hurricane', 'Immediate', 'Stay inside', 'Keep away from windows'];
    }

    private function existingInstruction(string $language = 'en', string $region = 'South'): Instruction
    {
        return Instruction::createFromResponse([
            'id' => 1, 'countryCode' => 'USA', 'eventType' => 'Hurricane', 'regionName' => $region,
            'attribution' => ['countryCode' => 'USA', 'name' => 'American Red Cross',
                'url' => null, 'imageUrl' => null, 'translations' => []],
            'translations' => [$language => [
                'id' => 20, 'lang' => $language, 'webUrl' => '', 'title' => 'Previous title',
                'description' => 'Previous description', 'stages' => [],
                'createdAt' => '2026-01-01 00:00:00', 'published' => false,
            ]],
        ]);
    }

    private function useExistingInstruction(Instruction $instruction): void
    {
        $this->client->shouldReceive('getLatestInstructionsByCountryCode')->with('USA')
            ->andReturn(new Collection([$instruction]));
    }

    public function test_import_warns_before_overwriting_an_existing_translation(): void
    {
        $this->useExistingInstruction($this->existingInstruction());
        $this->client->shouldNotReceive('createInstruction');
        $this->client->shouldNotReceive('updateInstruction');
        $this->expectException(RcnImportWillOverwriteException::class);

        (new RcnImporter($this->client))->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');
    }

    public function test_import_skips_existing_translation_when_overwriting_is_disabled(): void
    {
        $this->useExistingInstruction($this->existingInstruction());
        $this->client->shouldNotReceive('updateInstruction');
        $importer = new RcnImporter($this->client);
        $importer->turnWarningsOff();
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');

        $this->assertSame('skipped', $importer->getReport()['importSummary'][0]['importAction']);
        Event::assertNotDispatched(InstructionUpdatedViaImport::class);
    }

    public function test_import_overwrites_when_explicitly_enabled(): void
    {
        $this->useExistingInstruction($this->existingInstruction());
        $this->client->shouldReceive('updateInstruction')->once()->with(Mockery::on(function (Instruction $instruction) {
            return $instruction->getId() === 1
                && $instruction->getTranslationsByLanguage('en')->getTitle() === 'Hurricane safety';
        }))->andReturnUsing(fn ($instruction) => $instruction);
        $importer = new RcnImporter($this->client);
        $importer->turnWarningsOff();
        $importer->turnOverwritingOn();
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');

        $this->assertSame('updated', $importer->getReport()['importSummary'][0]['importAction']);
        Event::assertDispatched(InstructionUpdatedViaImport::class);
    }

    public function test_import_creates_instruction_with_parsed_stages_and_messages(): void
    {
        $this->client->shouldReceive('createInstruction')->once()->with(Mockery::on(function (Instruction $instruction) {
            $translation = $instruction->getTranslationsByLanguage('en');
            return $instruction->getCountryCode() === 'USA'
                && $instruction->getEventType() === 'Hurricane'
                && $instruction->getRegionName() === 'South'
                && $translation->getWebUrl() === 'https://example.test/safety'
                && $translation->getStages()->get('immediate') === [[
                    'title' => 'Stay inside', 'content' => ['Keep away from windows'],
                ]];
        }))->andReturnUsing(fn ($instruction) => $instruction);
        $importer = new RcnImporter($this->client);
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');

        $this->assertSame('added', $importer->getReport()['importSummary'][0]['importAction']);
        Event::assertDispatched(InstructionCreatedViaImport::class);
    }

    public function test_import_adds_a_new_language_without_replacing_existing_translation(): void
    {
        $this->useExistingInstruction($this->existingInstruction());
        $this->client->shouldReceive('updateInstruction')->once()->with(Mockery::on(function (Instruction $instruction) {
            return $instruction->getTranslationsByLanguage('en')->getTitle() === 'Previous title'
                && $instruction->getTranslationsByLanguage('fr')->getTitle() === 'Hurricane safety';
        }))->andReturnUsing(fn ($instruction) => $instruction);
        $importer = new RcnImporter($this->client);
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'fr');

        $this->assertSame('updated', $importer->getReport()['importSummary'][0]['importAction']);
    }

    public function test_import_does_not_match_an_instruction_in_another_region(): void
    {
        $this->useExistingInstruction($this->existingInstruction('en', 'North'));
        $this->client->shouldReceive('createInstruction')->once()->andReturnUsing(fn ($instruction) => $instruction);
        $this->client->shouldNotReceive('updateInstruction');
        $importer = new RcnImporter($this->client);
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');

        $this->assertSame('added', $importer->getReport()['importSummary'][0]['importAction']);
    }

    public function test_import_with_no_hazards_does_not_write_instructions(): void
    {
        $this->client->shouldNotReceive('createInstruction');
        $this->client->shouldNotReceive('updateInstruction');
        $importer = new RcnImporter($this->client);
        $importer->importFile($this->workbook(), 'USA', 'en');

        $this->assertSame([], $importer->getReport()['importSummary']);
    }

    public function test_import_rejects_an_unknown_organisation(): void
    {
        $this->client->shouldReceive('getOrganisationByCountryCode')->with('CAN')->andReturn(null);
        $this->client->shouldNotReceive('createInstruction');
        $this->expectException(RcnImportException::class);
        $this->expectExceptionMessage('No organization found');

        (new RcnImporter($this->client))->importFile($this->workbook(), 'CAN', 'en');
    }

    public function test_import_rejects_a_workbook_for_another_society(): void
    {
        $this->client->shouldNotReceive('createInstruction');
        $this->expectException(RcnImportException::class);
        $this->expectExceptionMessage('national society does not match');

        (new RcnImporter($this->client))->importFile($this->workbook([], 'Other Society'), 'USA', 'en');
    }

    public function test_import_rejects_a_missing_society(): void
    {
        $this->expectException(RcnImportException::class);
        $this->expectExceptionMessage('National society is missing');

        (new RcnImporter($this->client))->importFile($this->workbook([], ''), 'USA', 'en');
    }

    public function test_import_rejects_a_hazard_without_a_title(): void
    {
        $row = $this->hazardRow();
        $row[0] = '';
        $this->expectException(RcnImportException::class);
        $this->expectExceptionMessage('Title and/or description are missing');

        (new RcnImporter($this->client))->importFile($this->workbook([$row]), 'USA', 'en');
    }

    public function test_import_creates_instruction_when_api_has_no_existing_resource(): void
    {
        $this->client->shouldReceive('getLatestInstructionsByCountryCode')->with('USA')
            ->andThrow(new RcnApiResourceNotFoundException());
        $this->client->shouldReceive('createInstruction')->once()->andReturnUsing(fn ($instruction) => $instruction);
        $importer = new RcnImporter($this->client);
        $importer->importFile($this->workbook([$this->hazardRow()]), 'USA', 'en');

        $this->assertSame('added', $importer->getReport()['importSummary'][0]['importAction']);
    }
}
