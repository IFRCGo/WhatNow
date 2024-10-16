<?php

namespace App\Classes\RcnApi;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class RcnApiServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        $this->app->when(RcnApiClient::class)
            ->needs(ClientInterface::class)
            ->give(function () {
                return new Client([
                    'base_uri' => sprintf('%s/%s/', config('rcnapi.url'), config('rcnapi.version')),
                    'auth' => [
                        config('rcnapi.user'),
                        config('rcnapi.password'),
                    ]
                ]);
            });
    }
}
