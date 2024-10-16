<?php

namespace App\Classes\RcnApi\Importer;

use App\Classes\RcnApi\Importer\Exceptions\RcnImportInvalidFileException;

class ImportMetadata
{
    const ERROR_CODES = [
        'INVALID_COUNTRY_CODE' => 40001,
        'INVALID_LANGUAGE_CODE_MISMATCH' => 40002,
        'INVALID_INVALID_DATE' => 40003,
    ];

    
    protected $exportDate;

    
    protected $countryCode;

    
    protected $languageCode;

    
    public function __construct(

        array $metadataLine,
        string $countryCode,
        string $languageCode
    ) {
        $this->countryCode = $countryCode;
        $this->languageCode = $languageCode;
        $this->validateMetadata($metadataLine);
    }

    
    public function getExportDate(): \DateTimeImmutable
    {
        return $this->exportDate;
    }

    
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    
    private function validateMetadata(array $metadataLine)
    {
        $header = array_first($metadataLine);
        $header = ltrim($header, '#');
        $header = base64_decode($header);

        $countryCodeProvided = substr($header, 0, 3);
        $timestamp = substr($header, 3);

        if (mb_strlen($countryCodeProvided) !== 3) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_COUNTRY_CODE'],
                'Invalid country Code'
            );
        }

        if ($countryCodeProvided !== $this->countryCode) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_LANGUAGE_CODE_MISMATCH'],
                'File is not for this country code'
            );
        }

        try {
            $this->exportDate = new \DateTimeImmutable($timestamp);
        } catch (\Exception $e) {
            throw new RcnImportInvalidFileException(
                self::ERROR_CODES['INVALID_INVALID_DATE'],
                'Malformed timestamp'
            );
        }
    }
}
