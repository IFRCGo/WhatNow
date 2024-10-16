<?php

use App\Classes\RcnApi\Importer\Exceptions\RcnImportInvalidFileException;
use App\Classes\RcnApi\Importer\ImportMetadata;

class RcnImportMetadataTest extends \Tests\TestCase
{
    private $separator;

    public function setUp(): void
    {
        parent::setUp();
        $this->separator = trans('csvTemplate.separator');
    }

    public function test_metadata_validation_passes_with_valid_timestamp_metadata()
    {
        $timestamp = (new DateTime())->format('c');
        $line = [base64_encode(sprintf('USA%s', $timestamp))];
        $metadata = new ImportMetadata($line, 'USA', 'en');
        $this->assertInstanceOf(ImportMetadata::class, $metadata);
    }

    public function test_metadata_extracts_country_code()
    {
        $timestamp = (new DateTime())->format('c');
        $line = [base64_encode(sprintf('USA%s', $timestamp))];
        $metadata = new ImportMetadata($line, 'USA', 'en');
        $this->assertSame('USA', $metadata->getCountryCode());
    }

    public function test_metadata_validation_errors_with_missmatched_country_code()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        $timestamp = (new DateTime())->format('c');
        $line = [base64_encode(sprintf('USA%s', $timestamp))];
        new ImportMetadata($line, 'CAN', 'en');
    }

    public function test_metadata_validation_errors_with_invalid_timestamp_metadata()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        $timestamp = 'hello';
        $line = [sprintf('#USA %s %s', $this->separator, base64_encode($timestamp))];
        new ImportMetadata($line, 'USA', 'en');
    }

    public function test_metadata_validation_errors_with_blank_metadata()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        new ImportMetadata([], 'USA', 'en');
    }

    public function test_metadata_validation_errors_with_missing_country_code_metadata()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        $timestamp = (new DateTime())->format('c');
        $line = [sprintf('# %s %s', $this->separator, base64_encode($timestamp))];
        new ImportMetadata($line, 'USA', 'en');
    }

    public function test_metadata_validation_errors_with_missing_metadata_separator()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        $timestamp = (new DateTime())->format('c');
        $line = [sprintf('#USA %s', base64_encode($timestamp))];
        new ImportMetadata($line, 'USA', 'en');
    }

    public function test_metadata_validation_errors_with_alternative_separator()
    {
        $this->expectException(RcnImportInvalidFileException::class);
        $timestamp = (new DateTime())->format('c');
        $line = [sprintf('#USA %s %s', '||', base64_encode($timestamp))];
        new ImportMetadata($line, 'USA', 'en');
    }
}
