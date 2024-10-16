<?php

namespace App\Classes\RcnApi\Resources;

use App\Classes\RcnApi\Exceptions\RcnApiException;
use App\Classes\RcnApi\Exceptions\RcnApiAuthorisationException;
use App\Classes\RcnApi\Exceptions\RcnApiRequestException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceConflictException;
use App\Classes\RcnApi\Exceptions\RcnApiResourceNotFoundException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;

abstract class AbstractResource
{
    
    protected $http;

    
    protected $logger;

    
    public function __construct(ClientInterface $http, LoggerInterface $logger)
    {
        $this->http = $http;
        $this->logger = $logger;
    }

    
    protected function handleApiCall($callback)
    {
        try {
            return call_user_func($callback);
        } catch (ClientException $e) {
            if ($e->getCode() === Response::HTTP_FORBIDDEN) {
                throw new RcnApiAuthorisationException($e->getMessage(), $e->getCode(), $e);
            }

            if ($e->getCode() === Response::HTTP_NOT_FOUND) {
                throw new RcnApiResourceNotFoundException($e->getMessage(), $e->getCode(), $e);
            }

            if ($e->getCode() === Response::HTTP_CONFLICT) {
                throw new RcnApiResourceConflictException($e->getMessage(), $e->getCode(), $e);
            }

            $this->logger->error($e);
            throw new RcnApiRequestException($e->getMessage(), $e->getCode(), $e);
        } catch (TransferException $e) {
            $this->logger->error($e);
            throw new RcnApiException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
