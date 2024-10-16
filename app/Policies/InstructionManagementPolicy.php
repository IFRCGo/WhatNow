<?php

namespace App\Policies;

use App\Classes\RcnApi\Entities\Instruction;
use App\Models\Access\User\User as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstructionManagementPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->hasAll()) {
            return true;
        }
    }

    public function viewDrafts(User $user)
    {
        return $user->hasPermission('content-view');
    }

    public function listDrafts(User $user)
    {
        return $user->hasPermission('content-list');
    }

    public function create(User $user, Instruction $instruction)
    {
        return $user->hasPermission('content-create') && $user->hasOrganisationWithCountryCode($instruction->getCountryCode());
    }

    public function update(User $user, Instruction $instruction)
    {
        return $user->hasPermission('content-edit') && $user->hasOrganisationWithCountryCode($instruction->getCountryCode());
    }

    public function delete(User $user, Instruction $instruction)
    {
        return $user->hasPermission('content-delete') && $user->hasOrganisationWithCountryCode($instruction->getCountryCode());
    }

    public function publish(User $user, Instruction $instruction)
    {
        return $user->hasPermission('content-publish') && $user->hasOrganisationWithCountryCode($instruction->getCountryCode());
    }

    public function export(User $user   )
    {
        return $user->hasRoles(['GDPC Admin']);
    }

    public function publishMultiple(User $user)
    {
        return $user->hasPermission('content-publish');
    }
}
