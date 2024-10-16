<?php

namespace Tests;

use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Illuminate\Foundation\Testing\DatabaseMigrations;

Browser::macro('assertPageIs', function ($page) {
    if (! $page instanceof Page) {
        $page = new $page;
    }

    return $this->assertPathIs($page->url());
});

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
            '--no-sandbox'
        ]);

        $remoteWebDriver = env('DUSK_REMOTE_WEB_DRIVER', 'http://localhost:9515');

        return RemoteWebDriver::create(
            $remoteWebDriver, DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
