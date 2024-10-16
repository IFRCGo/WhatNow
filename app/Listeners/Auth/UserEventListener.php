<?php

namespace App\Listeners\Auth;

use App\Events\Backend\Auth\UserLoggedIn;
use App\Models\Access\User\User;
use App\Notifications\Auth\UserPasswordChanged;
use App\Repositories\Access\UserRepository;
use Illuminate\Auth\Events\PasswordReset;

class UserEventListener
{
    public static function onPasswordChanged(PasswordReset $event)
    {
        
        $user = $event->user;
        $user->notify(new UserPasswordChanged());
    }

    public static function onLogIn(UserLoggedIn $event)
    {
        
        $user = $event->user;
        $user->setLastLoggedIn();
    }

    
    public function subscribe($events)
    {
        $events->listen(
            PasswordReset::class,
            'App\Listeners\Auth\UserEventListener@onPasswordChanged'
        );

        $events->listen(
            UserLoggedIn::class,
            'App\Listeners\Auth\UserEventListener@onLogIn'
        );
    }
}
