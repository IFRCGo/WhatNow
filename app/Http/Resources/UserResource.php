<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

class UserResource extends Resource
{
    
    public function toArray($request)
    {
        $user = [
            'id' => $this->id,
            'email' => $this->email,
            'activated' => $this->activated,
            'password_updated_at' => ($this->password_updated_at) ? $this->password_updated_at->format('c') : null,
            'confirmed' => $this->confirmed,
            'last_logged_in_at' => ($this->last_logged_in_at) ? $this->last_logged_in_at->format('c') : null,
            'created_at' => $this->created_at->format('c'),
            'user_profile' => UserProfileResource::make($this->userProfile),
            'confirmed_role' => $this->confirmed_role,
        ];

        $roles = $this->whenLoaded('roles');
        if($roles instanceof Collection) {
            $user['role'] = RoleResource::make($roles->first());
        }

        $orgs = $this->whenLoaded('organisations');
        if ($orgs instanceof Collection) {
            $user['organisations'] = $orgs->pluck('organisation_code');
        }

        return $user;
    }
}
