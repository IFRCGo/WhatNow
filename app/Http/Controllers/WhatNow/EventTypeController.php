<?php

namespace App\Http\Controllers\WhatNow;


use App\Http\Controllers\ApiController;
use App\Http\Requests\EventTypeCreateRequest;
use App\Http\Resources\WhatNow\EventTypeResource;
use App\Models\EventType;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


final class EventTypeController extends ApiController
{


    public function list()
    {
        return EventTypeResource::collection(EventType::all());
    }

    protected function create(EventTypeCreateRequest $request)
    {
        $name = $request->get('name');
        $icon = $request->file('icon');
        $code =  Str::slug($name);
        $iconName = $code . '.' . $icon->getClientOriginalExtension();
        Storage::disk('public')->put($iconName,  File::get($icon));

        $event = EventType::create([
            'name' => $name,
            'icon' =>  $iconName,
            'code' => $code
        ]);

        return EventTypeResource::make($event)->response();
    }


}
