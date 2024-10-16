<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\Entities\Organisation;
use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Resources\WhatNowResource;
use App\Http\Controllers\ApiController;
use App\Http\Resources\WhatNow\OrganisationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrganisationController extends ApiController
{
    
    private $client;

    
    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->whatnow();
    }

    
    public function get(string $countryCode)
    {
        try {
            $organisations = $this->client->getOrganisationByCountryCode($countryCode);

            return OrganisationResource::make($organisations)->response();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    
    public function list()
    {
        try {
            $organisations = $this->client->getOrganisations();

            return OrganisationResource::collection($organisations);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function updateByCountryCode(Request $request, string $countryCode)
    {
        $this->validate($request, [
            'url' => 'url|max:255|nullable',
            'translations.*.name' => 'string|max:255',
            'translations.*.attributionMessage' => 'string|max:2048|nullable',
            'translations.*.published' => 'boolean'
        ]);

        $data = $request->except('countryCode');
        $data['countryCode'] = $countryCode;

        $organisation = Organisation::createFromArray($data);

        try {
            $organisation = $this->client->updateOrganisationByCountryCode($organisation);

            return OrganisationResource::make($organisation);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }
}
