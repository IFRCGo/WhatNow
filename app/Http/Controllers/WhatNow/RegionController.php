<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Http\Controllers\ApiController;
use App\Http\Resources\WhatNow\RegionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class RegionController extends ApiController
{
    
    private $client;


    
    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->whatnow();
    }


    public function getAllForOrganisation($country_code)
    {
        $regions  = $this->client->getAllForOrganisation($country_code);

        if(empty($regions)){
            return new JsonResponse(['error_message' => 'No regions found'], 404);
        }

        return new JsonResponse($regions, 200);
    }

    public function getForCountryCode($country_code, $code)
    {
        $regions = $this->client->getForCountryCode($country_code, $code);

        if(empty($regions)){
            return new JsonResponse(['error_message' => 'No regions found'], 404);
        }

        return new JsonResponse($regions, 200);
    }

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

    public function deleteRegion( $regionId)
    {
        $this->client->deleteRegion($regionId);

        return new JsonResponse(['message' => 'delete request accepted'], 202);
    }
}
