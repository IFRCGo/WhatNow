<?php

namespace Tests\Browser\Pages;

class Register extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/register';
    }

    /**
     * Submit the form with the given data.
     *
     * @param  \Laravel\Dusk\Browser $browser
     * @param  array $data
     * @return void
     */
    public function submit($browser, array $data = [], array $dropdowns = [])
    {
        foreach ($data as $key => $value) {
            $browser->type($key, $value);
        }

        foreach($dropdowns as  $key => $value) {
            $browser->select($key, $value);
        }

        $browser->press('Create Account')
            ->pause(500);
    }
}
