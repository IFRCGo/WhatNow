<?php

namespace App\Events\Backend\Access\User;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;


class UserDeleted extends Event
{
    use SerializesModels;

    
    public $user;

    
    public function __construct($user)
    {
        $this->user = $user;
    }
}
