<?php

namespace App\Providers;

use App\Listeners\Auth\UserEventListener;
use App\Listeners\WhatNow\InstructionListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    
    protected $listen = [
    ];

    protected $subscribe = [
        UserEventListener::class,
        InstructionListener::class
    ];

    
    public function boot()
    {
        parent::boot();



            }
}
