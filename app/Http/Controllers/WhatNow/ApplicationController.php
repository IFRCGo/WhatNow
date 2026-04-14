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
use Illuminate\Support\Facades\Log;


/**
 * @OA\Tag(
 *     name="Applications",
 *     description="Operations about applications"
 * )
 */
final class ApplicationController extends ApiController
{

    private $client;


    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->application();
    }

    /**
     * @OA\Get(
     *     path="/apps",
     *     tags={"Applications"},
     *     summary="List all applications",
     *     description="Retrieves a list of applications available for the authenticated user",
     *     operationId="listApplications",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Get(
     *     path="/apps/{id}",
     *     tags={"Applications"},
     *     summary="Get application by ID",
     *     description="Retrieves a specific application by its ID",
     *     operationId="getApplication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the application to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/apps",
     *     tags={"Applications"},
     *     summary="Create a new application",
     *     description="Creates a new application with the provided details",
     *     operationId="createApplication",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "estimatedUsers"},
     *             @OA\Property(property="name", type="string", description="Name of the application", example="MyApp"),
     *             @OA\Property(property="description", type="string", nullable=true, description="Description of the application", example="This is a sample application."),
     *             @OA\Property(property="estimatedUsers", type="integer", description="Estimated number of users", example=1000)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

        $user = $request->user()->loadMissing('roles', 'organisations');
        $userId = $user->id;

        try {
            $application = $this->client->createApplication($name, $description, $estimatedUsers, $userId);

            $userRules = [
                'can_access_legacy_whatnow' => (bool) $user->can_access_legacy_whatnow,
                'can_access_preparedness_v2' => (bool) $user->can_access_preparedness_v2,
            ];

            $currentRole = $user->roles->first();
            if ($currentRole && ! $currentRole->api_full_access) {
                $userRules['allowed_country_code'] = $user->organisations->pluck('organisation_code')->values()->all();
            }

            try {
                $this->client->updateRules($userId, $userRules);
            } catch (\Exception $e) {
                Log::error('Failed to update rules for user ' . $userId . ': ' . $e->getMessage());
            }

            return ApplicationResource::make($application);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }


    /**
     * @OA\Patch(
     *     path="/apps/{id}",
     *     tags={"Applications"},
     *     summary="Update application estimated users",
     *     description="Updates the estimated number of users for a specific application",
     *     operationId="updateApplication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the application to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"estimatedUsers"},
     *             @OA\Property(property="estimatedUsers", type="integer", description="Updated estimated number of users", example=5000)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/apps/{id}/activate",
     *     tags={"Applications"},
     *     summary="Activate an application",
     *     description="Activates a specific application by its ID",
     *     operationId="activateApplication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the application to activate",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function activate(Request $request, int $id)
    {
        try {
            $application = $this->client->activateApplication($id);

            return ApplicationResource::make($application);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * @OA\Patch(
     *     path="/apps/{id}/deactivate",
     *     tags={"Applications"},
     *     summary="Deactivate an application",
     *     description="Deactivates a specific application by its ID",
     *     operationId="deactivateApplication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the application to deactivate",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function deactivate(Request $request, int $id)
    {
        try {
            $application = $this->client->deactivateApplication($id);

            return ApplicationResource::make($application);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/apps/{id}",
     *     tags={"Applications"},
     *     summary="Delete an application",
     *     description="Deletes an application by its ID",
     *     operationId="deleteApplication",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the application to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
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
