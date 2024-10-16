<?php

namespace Tests\Unit\Renderer;

use App\Classes\Renderer\Entities\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function test_it_returns_correct_language_code()
    {
        $lang = new Language('en');
        $this->assertEquals('en', $lang->getCode());
    }

    /**
     * @dataProvider getLanguageCodesWithRtlResult
     */
    public function test_it_returns_correct_rtl_status($code, $isRtl)
    {
        $lang = new Language($code);
        $this->assertEquals($isRtl, $lang->isRtl());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getLanguageCodesWithRtlResult()
    {
        $rtl = array_map(function ($code) {
            return [$code, true];
        }, Language::getRtlLangCodes());

        $ltr = array_map(function ($code) {
            return [$code, false];
        }, ['en', 'fr', 'de']);

        return array_merge($rtl, $ltr);
    }
}
