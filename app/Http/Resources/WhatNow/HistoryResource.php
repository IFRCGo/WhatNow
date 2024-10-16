<?php

namespace App\Http\Resources\WhatNow;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\Resource;

class HistoryResource extends Resource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'action' => trans($this->action),
            'user_id' => $this->user_id,
            'entity_id' => $this->entity_id,
            'content' => $this->content,
            'country_code' => $this->country_code,
            'language_code' => $this->language_code,
            'created_at' => $this->created_at->format('c'),
            'user' => UserResource::make($this->user)
        ];
    }
}
