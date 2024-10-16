<?php

namespace App\Models\Access\Permission;

use Illuminate\Database\Eloquent\Model;


class Permission extends Model
{
    
    protected $table;

    
    protected $fillable = ['name', 'display_name', 'sort'];

    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'permissions';
    }

    
    public function roles()
    {
        return $this->belongsToMany('roles', 'role_permissions', 'permission_id',
            'role_id');
    }
}
