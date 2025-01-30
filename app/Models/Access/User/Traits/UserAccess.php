<?php

namespace App\Models\Access\User\Traits;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use App\Models\Access\User\UserOrganisation;


trait UserAccess
{
    public function hasAll()
    {
        foreach ($this->roles as $role) {
                        if ($role->all) {
                return true;
            }
        }

        return false;
    }

    
    public function hasRole($nameOrId)
    {
        foreach ($this->roles as $role) {
                        if ($role->all) {
                return true;
            }

                        if (is_numeric($nameOrId)) {
                if ($role->id == $nameOrId) {
                    return true;
                }
            }

                        if ($role->name == $nameOrId) {
                return true;
            }
        }

        return false;
    }

    
    public function hasRoles($roles)
    {
                $hasRoles = 0;
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                $hasRoles++;
            }
        }

        return $hasRoles > 0;
    }

    
    public function hasAllRoles($roles)
    {
        $hasRoles = 0;
        $numRoles = count($roles);

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                $hasRoles++;
            }
        }

        return $numRoles == $hasRoles;
    }

    public function getMostAuthoritativeRoleSortValue()
    {
        $roleSortValues = [];

        foreach ($this->roles as $role) {
            $roleSortValues[] = $role->sort;
        }

        if (empty($roleSortValues)) {
            throw new \Exception('Users must have at least one role');
        }

                return min($roleSortValues);
    }

    
    public function allow($nameOrId)
    {
        
        foreach ($this->roles as $role) {
            if ($role->hasPermission($nameOrId)) {
                return true;
            }
        }

        return false;
    }

    
    public function allowMultiple($permissions, $needsAll = false)
    {
                if ($needsAll) {
            $hasPermissions = 0;
            $numPermissions = count($permissions);

            foreach ($permissions as $perm) {
                if ($this->allow($perm)) {
                    $hasPermissions++;
                }
            }

            return $numPermissions == $hasPermissions;
        }

                $hasPermissions = 0;
        foreach ($permissions as $perm) {
            if ($this->allow($perm)) {
                $hasPermissions++;
            }
        }

        return $hasPermissions > 0;
    }

    
    public function hasPermission($nameOrId)
    {
        return $this->allow($nameOrId);
    }

    
    public function hasPermissions($permissions, $needsAll = false)
    {
        return $this->allowMultiple($permissions, $needsAll);
    }

    
    public function attachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role, ['updated_at' => now()]);
    }

    
    public function detachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->detach($role);
    }

    
    public function attachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->attachRole($role);
        }
    }

    
    public function detachRoles($roles)
    {
        foreach ($roles as $role) {
            $this->detachRole($role);
        }
    }

    
    public function getPermissions()
    {
        $permissions = [];

        
        foreach ($this->roles as $role) {
            if ($role->hasAll()) {
                $rolePermmissions = Permission::all();
            } else {
                $rolePermmissions = $role->permissions;
            }
            $permissions = array_merge($permissions, $rolePermmissions->pluck('name')->toArray());
        }

        return array_unique($permissions);
    }

    
    public function hasOrganisationWithCountryCode(string $countryCode)
    {
        if ($this->hasPermission('organisations_all')) {
            return true;
        }

        $matches = $this->organisations->reject(function (UserOrganisation $organisation) use ($countryCode) {
            return $organisation->organisation_code !== $countryCode;
        });

        return ($matches->count() > 0);
    }
}
