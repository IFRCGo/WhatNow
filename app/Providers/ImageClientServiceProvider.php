<?php

namespace App\Providers;

use App\Classes\Renderer\Contracts\ImageClientInterface;
use App\Classes\Renderer\Entities\SnappyImageClient;
use Illuminate\Support\ServiceProvider;
use Knp\Snappy\Image;

class ImageClientServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
            }

    
    public function register()
    {
        $this->app->bind(ImageClientInterface::class, function ($app) {
            return $app->make(SnappyImageClient::class);
        });

        $this->app->bind(Image::class, function () {
            return new Image(config('whatnowimage.binary'));
        });
    }
}
