<?php

namespace App\Http\Controllers\WhatNow;

use OpenApi\Attributes as OA;

use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Http\Controllers\ApiController;
use App\Http\Resources\WhatNow\RegionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

#[OA\Tag(
    name: 'Regions',
    description: 'Operations about Regions',
)]
final class RegionController extends ApiController
{

    private $client;



    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->whatnow();
    }


    #[OA\Get(
        path: '/subnationals/{country_code}',
        tags: ['Regions'],
        summary: 'Get all subnationals for an organisation',
        description: 'Retrieves all subnationals associated with a given country code',
        operationId: 'getAllRegionsForOrganisation',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'country_code',
            in: 'path',
            required: true,
            description: 'Country code for which the subnationals are retrieved',
            schema: new OA\Schema(
                type: 'string',
                example: 'USA',
            ),
        )],
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
    public function getAllForOrganisation($country_code)
    {
        $regions  = $this->client->getAllForOrganisation($country_code);

        if(empty($regions)){
            return new JsonResponse(['error_message' => 'No subnationals found'], 404);
        }

        return new JsonResponse($regions, 200);
    }

    #[OA\Get(
        path: '/subnationals/{country_code}/{code}',
        tags: ['Regions'],
        summary: 'Get a specific subnationals by country code and subnationals code',
        description: 'Retrieves a specific subnationals based on the provided country code and subnationals code',
        operationId: 'getRegionForCountryCode',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'country_code',
            in: 'path',
            required: true,
            description: 'Country code to filter the subnationals',
            schema: new OA\Schema(
                type: 'string',
                example: 'USA',
            ),
        ), new OA\Parameter(
            name: 'code',
            in: 'path',
            required: true,
            description: 'Region code to filter the subnationals',
            schema: new OA\Schema(
                type: 'string',
                example: 'NY',
            ),
        )],
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
    public function getForCountryCode($country_code, $code)
    {
        $regions = $this->client->getForCountryCode($country_code, $code);

        if(empty($regions)){
            return new JsonResponse(['error_message' => 'No subnationals found'], 404);
        }

        return new JsonResponse($regions, 200);
    }

    #[OA\Post(
        path: '/subnationals',
        tags: ['Regions'],
        summary: 'Create a new subnationals',
        description: 'Creates a new subnationals with the provided details',
        operationId: 'createRegion',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['countryCode', 'title'],
                properties: [new OA\Property(
                    property: 'countryCode',
                    type: 'string',
                    description: 'Country code (3-letter ISO format)',
                    example: 'USA',
                ), new OA\Property(
                    property: 'title',
                    type: 'string',
                    description: 'Title of the subnationals',
                    example: 'New York',
                ), new OA\Property(
                    property: 'slug',
                    type: 'string',
                    nullable: true,
                    description: 'Slug for the subnationals',
                    example: 'new-york',
                ), new OA\Property(
                    property: 'translations',
                    type: 'array',
                    description: 'Array of translations',
                    items: new OA\Items(
                        type: 'object',
                        properties: [new OA\Property(
                            property: 'webUrl',
                            type: 'string',
                            format: 'url',
                            nullable: true,
                            description: 'Optional URL for additional information',
                            example: 'https://example.com',
                        ), new OA\Property(
                            property: 'lang',
                            type: 'string',
                            description: 'Language code (2-letter ISO format)',
                            example: 'en',
                        ), new OA\Property(
                            property: 'title',
                            type: 'string',
                            description: 'Title translation',
                            example: 'New York',
                        ), new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'Description translation',
                            example: 'New York region',
                        )],
                    ),
                )],
            ),
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
    public function createRegion(Request $request)
    {
        try {
            $input = $this->validate($request, [
                'countryCode' => 'required|string|size:3',
                'title' => 'required|string',
                'slug' => 'sometimes|string',
                'translations' => 'array',
                'translations.*.webUrl' => 'url',
                'translations.*.lang' => 'alpha|size:2',
                'translations.*.title' => 'string',
                'translations.*.description' => 'string'
            ]);
        } catch (ValidationException $e) {
            return $e->getResponse();
        }

        try{
            $region = $this->client->createRegion($input);
        } catch (\Exception $e) {
            return new JsonResponse(['error_message' => $e->getMessage()], 500);
        }

        return new JsonResponse($region, 201);
    }

    #[OA\Put(
        path: '/subnationals/subnationals/{regionId}',
        tags: ['Regions'],
        summary: 'Update an existing subnationals',
        description: 'Updates a subnationals with new data',
        operationId: 'updateRegion',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'regionId',
            in: 'path',
            required: true,
            description: 'ID of the subnationals to update',
            schema: new OA\Schema(
                type: 'integer',
                example: 1,
            ),
        )],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['countryCode', 'title'],
                properties: [new OA\Property(
                    property: 'countryCode',
                    type: 'string',
                    description: 'Country code (3-letter ISO format)',
                    example: 'USA',
                ), new OA\Property(
                    property: 'title',
                    type: 'string',
                    description: 'Title of the subnationals',
                    example: 'New York',
                ), new OA\Property(
                    property: 'slug',
                    type: 'string',
                    nullable: true,
                    description: 'Slug for the subnationals',
                    example: 'new-york',
                ), new OA\Property(
                    property: 'translations',
                    type: 'array',
                    description: 'Array of translations',
                    items: new OA\Items(
                        type: 'object',
                        properties: [new OA\Property(
                            property: 'webUrl',
                            type: 'string',
                            format: 'url',
                            nullable: true,
                            description: 'Optional URL for additional information',
                            example: 'https://example.com',
                        ), new OA\Property(
                            property: 'lang',
                            type: 'string',
                            description: 'Language code (2-letter ISO format)',
                            example: 'en',
                        ), new OA\Property(
                            property: 'title',
                            type: 'string',
                            description: 'Title translation',
                            example: 'New York',
                        ), new OA\Property(
                            property: 'description',
                            type: 'string',
                            description: 'Description translation',
                            example: 'New York region',
                        )],
                    ),
                )],
            ),
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
    public function updateRegion(Request $request, $regionId)
    {
        try {
            $input = $this->validate($request, [
                'countryCode' => 'required|string|size:3',
                'title' => 'required|string',
                'slug' => 'sometimes|string',
                'translations' => 'array',
                'translations.*.webUrl' => 'url',
                'translations.*.lang' => 'alpha|size:2',
                'translations.*.title' => 'string',
                'translations.*.description' => 'string'
            ]);
        } catch (ValidationException $e) {
            return $e->getResponse();
        }

        try{
            $region = $this->client->updateRegion($regionId, $input);
        } catch (\Exception $e) {
            return new JsonResponse(['error_message' => $e->getMessage()], 500);
        }

        return new JsonResponse($region, 202);
    }

    #[OA\Delete(
        path: '/subnationals/subnationals/{regionId}',
        tags: ['Regions'],
        summary: 'Delete a subnationals',
        description: 'Deletes a subnationals by its ID',
        operationId: 'deleteRegion',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'regionId',
            in: 'path',
            required: true,
            description: 'ID of the subnationals to delete',
            schema: new OA\Schema(
                type: 'integer',
                example: 1,
            ),
        )],
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
    public function deleteRegion( $regionId)
    {
        $this->client->deleteRegion($regionId);

        return new JsonResponse(['message' => 'delete request accepted'], 202);
    }
}
