<?php

namespace App\Http\Controllers\WhatNow;

use OpenApi\Attributes as OA;


use App\Http\Controllers\ApiController;
use App\Http\Requests\EventTypeCreateRequest;
use App\Http\Resources\WhatNow\EventTypeResource;
use App\Models\EventType;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Classes\RcnApi\RcnApiClient;
use PhpParser\Node\Stmt\TryCatch;

#[OA\Tag(
    name: 'EventTypes',
    description: 'Operations about Event Types',
)]
final class EventTypeController extends ApiController
{
    protected $rcnApiClient;

    public function __construct(RcnApiClient $rcnApiClient)
    {
        $this->rcnApiClient = $rcnApiClient;
    }

    #[OA\Get(
        path: '/event-types/',
        tags: ['EventTypes'],
        summary: 'Get a list of event types',
        description: 'Returns a list of all event types',
        operationId: 'listEventTypes',
        security: [['bearerAuth' => []]],
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
    public function list()
    {
        return EventTypeResource::collection(EventType::all());
    }

    #[OA\Post(
        path: '/event-types/',
        tags: ['EventTypes'],
        summary: 'Create a new event type',
        description: 'Creates a new event type with the provided name and icon',
        operationId: 'createEventType',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'icon'],
                    properties: [new OA\Property(
                        property: 'name',
                        type: 'string',
                        description: 'The name of the event type',
                    ), new OA\Property(
                        property: 'icon',
                        type: 'string',
                        format: 'binary',
                        description: 'The icon file for the event type',
                    )],
                ),
            )],
        ),
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
    protected function create(EventTypeCreateRequest $request)
    {
        $name = $request->get('name');
        $icon = $request->file('icon');
        $code =  Str::slug($name);
        $iconName = $code . '.' . $icon->getClientOriginalExtension();
        $filePath = $icon->getPathname();
        
        try {
           $this->rcnApiClient->whatnow()->uploadFile($filePath, $iconName);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }

        $event = EventType::create([
            'name' => $name,
            'icon' =>  $iconName,
            'code' => $code
        ]);

        return EventTypeResource::make($event)->response();
    }


}
