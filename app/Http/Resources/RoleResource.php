<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'super' => (bool) $this->all,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
