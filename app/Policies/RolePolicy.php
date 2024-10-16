<?php

namespace App\Policies;

use App\Models\Access\User\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->hasAll()) {
            return true;
        }
    }

    public function list(User $user)
    {
        return $user->hasPermission('users-list');
    }
}
