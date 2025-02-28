<?php

namespace App\Http\Controllers\WhatNow;


use App\Http\Controllers\ApiController;
use App\Http\Requests\EventTypeCreateRequest;
use App\Http\Resources\WhatNow\EventTypeResource;
use App\Models\EventType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="EventTypes",
 *     description="Operations about Event Types"
 * )
 */
final class EventTypeController extends ApiController
{

    /**
     * @OA\Get(
     *     path="/event-types/",
     *     tags={"EventTypes"},
     *     summary="Get a list of event types",
     *     description="Returns a list of all event types",
     *     operationId="listEventTypes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function list()
    {
        return EventTypeResource::collection(EventType::all());
    }

    /**
     * @OA\Post(
     *     path="/event-types/",
     *     tags={"EventTypes"},
     *     summary="Create a new event type",
     *     description="Creates a new event type with the provided name and icon",
     *     operationId="createEventType",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "icon"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The name of the event type"
     *                 ),
     *                 @OA\Property(
     *                     property="icon",
     *                     type="string",
     *                     format="binary",
     *                     description="The icon file for the event type"
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
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
