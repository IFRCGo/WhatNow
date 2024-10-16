<?php

namespace App\Events\Backend\Auth;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;


class UserLoggedOut extends Event
{
    use SerializesModels;

    
    public $user;

    
    public function __construct($user)
    {
        $this->user = $user;
    }
}
