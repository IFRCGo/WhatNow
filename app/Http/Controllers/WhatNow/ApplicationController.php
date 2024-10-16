<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\Exceptions\RcnApiAuthorisationException;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\RcnApiClient;
use App\Http\Controllers\ApiController;
use App\Http\Resources\WhatNow\ApplicationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ApplicationController extends ApiController
{
    
    private $client;

    
    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->application();
    }

    
    public function list(Request $request)
    {
        $userId = $request->user()->id;

        try {
            $applications = $this->client->getApplicationsForUser($userId);

            return ApplicationResource::collection($applications);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    
    public function get(int $id)
    {
        try {
            $application = $this->client->getAppById($id);

            return ApplicationResource::make($application);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'string',
            'estimatedUsers' => 'required|integer',
        ]);

        $name = $request->get('name');
        $description = $request->get('description');
        $estimatedUsers = $request->get('estimatedUsers', 0);

        $userId = $request->user()->id;

        try {
            $application = $this->client->createApplication($name, $description, $estimatedUsers, $userId);

            return ApplicationResource::make($application);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    
     public function update(Request $request, int $id)
     {
         $this->validate($request, [
             'estimatedUsers' => 'required|integer',
         ]);

         $estimatedUsers = $request->get('estimatedUsers', 0);

         try {
             $application = $this->client->updateApplication($id, $estimatedUsers);

             return ApplicationResource::make($application);
         } catch (RcnApiResourceNotFoundException $e) {
             return $this->respondWithNotFound($e);
         } catch (RcnApiException $e) {
             return $this->respondWithError($e);
         }
     }

    
    public function delete(int $id)
    {
        try {
            $this->client->deleteApplication($id);

            return $this->respondWithSuccess();
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiAuthorisationException $e) {
            return $this->respondWithForbidden($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }
}
