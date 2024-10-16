<?php

namespace App\Http\Resources\WhatNow;

use Illuminate\Http\Resources\Json\Resource;

class EventTypeResource extends Resource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'icon' => $this->icon,
            'code' => $this->code,
            'url' => asset('storage/' . $this->icon)
        ];
    }
}
