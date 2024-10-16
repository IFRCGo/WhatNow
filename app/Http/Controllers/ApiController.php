<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    
    public function respondWithBadRequest(\Exception $originalException, string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.bad_request');
        }

        $response = [
            'message' => $message
        ];

        if (config('app.debug') === true) {
            $response['trace'] = $originalException->getTraceAsString();
        }

        return new JsonResponse($response, JsonResponse::HTTP_BAD_REQUEST);
    }

    
    public function respondWithError(\Exception $originalException, string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.general_error');
        }

        $response = [
            'message' => $message
        ];

        if (config('app.debug') === true) {
            $response['error'] = $originalException->getMessage();
            $response['trace'] = $originalException->getTraceAsString();
        }

        return new JsonResponse($response, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    
    public function respondWithNotFound(\Exception $originalException, string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.not_found');
        }

        $response = [
            'message' => $message
        ];

        if (config('app.debug') === true) {
            $response['trace'] = $originalException->getTraceAsString();
        }

        return new JsonResponse($response, JsonResponse::HTTP_NOT_FOUND);
    }


    
    public function respondWithConflict(\Exception $originalException, string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.conflict.general');
        }

        $response = [
            'message' => $message
        ];

        if (config('app.debug') === true) {
            $response['trace'] = $originalException->getTraceAsString();
        }

        return new JsonResponse($response, JsonResponse::HTTP_CONFLICT);
    }

    
    public function respondWithForbidden(\Exception $originalException, string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.forbidden');
        }

        $response = [
            'message' => $message
        ];

        if (config('app.debug') === true) {
            $response['trace'] = $originalException->getTraceAsString();
        }

        return new JsonResponse($response, JsonResponse::HTTP_FORBIDDEN);
    }

    
    public function respondWithSuccess(string $message = null)
    {
        if ($message === null) {
            $message = trans('rcnapi.success');
        }

        return new JsonResponse(['message' => $message], JsonResponse::HTTP_OK);
    }
}
