<?php

namespace App\Events\Backend\Auth;

use App\Events\Event;
use App\Models\Access\User\User;
use Illuminate\Queue\SerializesModels;


class UserPasswordSet extends Event
{
    use SerializesModels;

    
    public $user;

    
    public function __construct($user)
    {
        $this->user = $user;
    }
}
