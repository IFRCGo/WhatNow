<?php

namespace App\Http\Resources\WhatNow;

use Illuminate\Http\Resources\Json\JsonResource;

class EventTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'icon' => $this->icon,
            'code' => $this->code,
            'url' => $this->getIconUrl(),
        ];
    }
}
