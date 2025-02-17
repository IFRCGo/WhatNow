<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'super' => (bool) $this->all,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
