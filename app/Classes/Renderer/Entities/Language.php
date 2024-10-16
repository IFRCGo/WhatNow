<?php

namespace App\Classes\Renderer\Entities;

class Language
{
    protected $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isRtl(): bool
    {
        if (in_array($this->getCode(), self::getRtlLangCodes())) {
            return true;
        }
        return false;
    }

    public static function getRtlLangCodes(): array
    {
        return [
            'ar',
            'dv',
            'fa',
            'he',
            'ku',
            'ps',
            'ur',
        ];
    }
}
