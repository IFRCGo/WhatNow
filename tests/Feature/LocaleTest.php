<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    use ReadsFrontendTranslations;

    #[\PHPUnit\Framework\Attributes\Test]
    public function fetch_translations()
    {
        $this->getJson('/api/translations/en')
            ->assertSuccessful()
            ->assertJson(['ok' => $this->trans('ok')]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function set_locale_from_header()
    {
        $this->withHeaders(['Accept-Language' => 'zh-CN'])
            ->postJson('/api/login');

        $this->assertEquals('zh-CN', $this->app->getLocale());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function set_locale_from_header_short()
    {
        $this->withHeaders(['Accept-Language' => 'en-US'])
            ->postJson('/api/login');

        $this->assertEquals('en', $this->app->getLocale());
    }
}
