<?php

namespace App\Providers;

use App\Models\Terms;
use App\Repositories\TermsRepository;
use App\Repositories\TermsRepositoryInterface;
use Laravel\Dusk\DuskServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }

        $this->app->bind(TermsRepositoryInterface::class, TermsRepository::class);

        View::composer('scripts', function ($view) {
            $termsRepo = $this->app->make(TermsRepositoryInterface::class);
            $latest = $termsRepo->getLatest();
            $version = ($latest instanceof Terms) ? $latest->version : 0;
            $view->with('terms_version', $version);
        });
    }

    
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
