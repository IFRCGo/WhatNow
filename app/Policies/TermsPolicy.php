<?php

namespace App\Policies;

use App\Models\Access\User\User as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermsPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->hasAll()) {
            return true;
        }
    }

    public function update(User $user)
    {
        return $user->hasPermission('terms-update');
    }
}
