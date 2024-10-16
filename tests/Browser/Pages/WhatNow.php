<?php

namespace Tests\Browser\Pages;

class WhatNow extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/admin/content/whatnow';
    }

    /**
     * Click on the log out link.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @return void
     */
    public function clickLogout($browser)
    {
        $browser->clickLink('Logout')
            ->pause(300);
    }
}
