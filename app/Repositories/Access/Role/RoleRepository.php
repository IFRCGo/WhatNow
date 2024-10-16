<?php

namespace App\Repositories\Access\Role;

use App\Models\Access\Role\Role;
use App\Repositories\Repository;


class RoleRepository extends Repository
{
    
    const MODEL = Role::class;

    
    public function getAll($order_by = 'sort', $sort = 'asc')
    {
        return $this->query()
            ->with('permissions')
            ->orderBy($order_by, $sort)
            ->get();
    }
}
