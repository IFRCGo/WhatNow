<?php

use App\Classes\RcnApi\Importer\ImportMetadata;
use PHPUnit\Framework\TestCase;

class RcnImportMetadataTest extends TestCase
{
    public function test_metadata_preserves_country_language_and_subnational(): void
    {
        $metadata = new ImportMetadata('USA', 'en', 'South');

        $this->assertSame('USA', $metadata->getCountryCode());
        $this->assertSame('en', $metadata->getLanguageCode());
        $this->assertSame('South', $metadata->getRegion());
    }

    public function test_metadata_supports_national_imports(): void
    {
        $metadata = new ImportMetadata('CAN', 'fr', 'National');

        $this->assertSame('CAN', $metadata->getCountryCode());
        $this->assertSame('fr', $metadata->getLanguageCode());
        $this->assertSame('National', $metadata->getRegion());
    }

    public function test_metadata_preserves_an_empty_region_for_national_fallback(): void
    {
        $metadata = new ImportMetadata('USA', 'es', '');

        $this->assertSame('', $metadata->getRegion());
        $this->assertSame('es', $metadata->getLanguageCode());
    }
}
