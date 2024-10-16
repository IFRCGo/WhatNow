<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Access\Role\Role;
use App\Repositories\Access\Role\RoleRepository;

class RoleController extends Controller
{
    
    private $roles;

    
    public function __construct(RoleRepository $roles)
    {
        $this->roles = $roles;
    }

    
    public function listRoles()
    {
        $this->authorize('list', Role::class);

        return RoleResource::collection($this->roles->getAll());
    }
}
