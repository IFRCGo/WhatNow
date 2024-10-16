<?php

namespace App\Http\Controllers\Alert;

use App\Classes\RcnApi\RcnApiClient;
use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use App\Classes\RcnApi\Resources\AlertResource as RcnAlertResource;
use App\Http\Controllers\ApiController;
use App\Http\Resources\Alert\AlertResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AlertController extends ApiController
{
    
    private $client;

    
    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->alert();
    }

    
    public function listByCountryCode($code)
    {
        try {
            $alerts = $this->client->getAlertsForOrganisation($code);

            return AlertResource::collection($alerts);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }

    public function getByIdentifier($identifier)
    {
        try {
            $alert = $this->client->getByIdentifier($identifier);

            return AlertResource::make($alert);
        } catch (RcnApiResourceNotFoundException $e) {
            return $this->respondWithNotFound($e);
        } catch (RcnApiException $e) {
            return $this->respondWithError($e);
        }
    }
}
