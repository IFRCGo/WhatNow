<?php

namespace App\Models\Access\User\Traits\Relationship;
use App\Models\Access\Role\Role;
use App\Models\Access\User\UserOrganisation;
use App\Models\Access\User\UserProfile;
use App\Models\History\History;


trait UserRelationship
{

    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }

    
    public function organisations()
    {
        return $this->hasMany(UserOrganisation::class);
    }

    
    public function history()
    {
        return $this->hasMany(History::class);
    }

}
