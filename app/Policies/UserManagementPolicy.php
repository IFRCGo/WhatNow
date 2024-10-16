<?php

namespace App\Policies;

use App\Models\Access\User\User as User;
use App\Models\Access\User\User as UserModel;
use App\Models\Access\User\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserManagementPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if($user->hasAll()){
            return true;
        }
    }

    public function list(User $user)
    {
        return $user->hasPermission('users-list');
    }

    public function view(User $user, UserModel $model)
    {
        return $user->hasPermission('users-view') || $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->hasPermission('users-create');
    }

    public function update(User $user, UserModel $model)
    {
        return $user->hasPermission('users-edit') || $user->id === $model->id;
    }

    public function test(User $user, UserProfile $model)
    {
        return $user->hasPermission('users-edit') || $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        return $user->hasPermission('users-delete') && $user->id !== $model->id;
    }

    public function deactivate(User $user)
    {
        return $user->hasPermission('users-deactivate');
    }

    public function reactivate(User $user)
    {
        return $user->hasPermission('users-reactivate');
    }
}
