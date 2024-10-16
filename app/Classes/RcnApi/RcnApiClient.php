<?php

namespace App\Classes\RcnApi;

use App\Classes\RcnApi\Resources\AlertResource;
use App\Classes\RcnApi\Resources\ApplicationResource;
use App\Classes\RcnApi\Resources\UsageResource;
use App\Classes\RcnApi\Resources\WhatNowResource;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;

class RcnApiClient
{
    
    protected $http;

    
    protected $logger;

    
    public function __construct(ClientInterface $http, LoggerInterface $logger)
    {
        $this->http = $http;
        $this->logger = $logger;
    }

    
    public function whatnow(): WhatNowResource
    {
        return new WhatNowResource($this->http, $this->logger);
    }

    
    public function application(): ApplicationResource
    {
        return new ApplicationResource($this->http, $this->logger);
    }

    
    public function alert(): AlertResource
    {
        return new AlertResource($this->http, $this->logger);
    }

    
    public function usage(): UsageResource
    {
        return new UsageResource($this->http, $this->logger);
    }
}
