<?php

namespace App\Events\Backend\Access\Role;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;


class RoleUpdated extends Event
{
    use SerializesModels;

    
    public $role;

    
    public function __construct($role)
    {
        $this->role = $role;
    }
}
