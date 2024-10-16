<?php

namespace App\Http\Resources\Terms;

use App\Http\Resources\UserResource;
use App\Models\Access\User\User;
use Illuminate\Http\Resources\Json\Resource;

class TermsResource extends Resource
{
    public function toArray($request)
    {
        return [
            'version' => $this->version,
            'content' => $this->content,
            'createdAt' => $this->created_at->format('c'),
            'user' => UserResource::make($this->whenLoaded('user'))
        ];
    }
}
