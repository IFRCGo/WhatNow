<?php

namespace App\Models\Access\Role;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Traits\RoleAccess;
use App\Models\Access\User\User;
use Illuminate\Database\Eloquent\Model;


class Role extends Model
{
    use RoleAccess;

    const ROLE_API_USER = 1;
    const ROLE_3SC_ADMIN = 2;
    const ROLE_GDPC_ADMIN = 3;
    const ROLE_NS_ADMIN = 4;
    const ROLE_NS_EDITOR = 5;

    const ROLE_DEFAULT = self::ROLE_API_USER; 
    
    protected $table;

    
    protected $fillable = ['name', 'all', 'sort'];

    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'roles';
    }

    
    public function hasPermission($nameOrId, $allowAll = true)
    {
                if ($allowAll === true && $this->all) {
            return true;
        }

                foreach ($this->permissions as $perm) {

                        if (is_numeric($nameOrId)) {
                if ($perm->id == $nameOrId) {
                    return true;
                }
            }

                        if ($perm->name == $nameOrId) {
                return true;
            }
        }

        return false;
    }

    
    public function scopeSort($query, $direction = "asc")
    {
        return $query->orderBy('sort', $direction);
    }

    
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class, 'role_permissions', 'role_id','permission_id'
        )->orderBy('display_name', 'asc');
    }

    
    public function hasAll()
    {
        return (bool) $this->all;
    }
}
