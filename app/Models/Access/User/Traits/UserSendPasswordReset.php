<?php

namespace App\Models\Access\User\Traits;

use App\Notifications\Auth\UserNeedsPasswordReset;


trait UserSendPasswordReset
{
    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserNeedsPasswordReset($token));
    }
}
