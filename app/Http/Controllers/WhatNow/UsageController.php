<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\RcnApiClient;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

final class UsageController extends ApiController
{
    
    private $client;

    
    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->usage();
    }

    
    public function listApplicationRequestCount(Request $request): JsonResponse
    {
        $this->validate($request, [
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'page' => 'sometimes|integer',
        ]);

        $response = $this->client->listApplicationRequestCount($request->fromDate, $request->toDate, $request->page ?? 1);

        return new JsonResponse($response, 200);
    }

    
    public function listEndpointRequestCount(Request $request): JsonResponse
    {
        $this->validate($request, [
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'page' => 'sometimes|integer',
        ]);

        $applications = collect([]);
        $rows = collect([]);

        $response = $this->client->listEndpointRequestCount($request->fromDate, $request->toDate, $request->page ?? 1);

        $response->each(function ($endpoint) use ($applications) {
            $endpoint->each(function ($application) use ($applications) {
                $userModel = \App\Models\Access\User\User::with('userProfile')->find($application['application_tenant_user_id']);
                $organisationName = $userModel ? $userModel->userProfile->organisation : 'N/A';

                $applications->push([
                    'organisation_name' => $organisationName,
                    'application_name' => $application['application_name'],
                    'endpoint' => $application['endpoint'],
                    'hit_count' => $application['hit_count'],
                ]);
            });
        });

        $applications->groupBy('organisation')->each(function ($organisation) use ($rows) {
            $index = 0;

            $organisation->sortBy('application_name')->each(function ($application) use (&$index, $rows) {
                $rows->push([
                    'organisation_name' => $application['organisation_name'],
                    'application_name' => $application['application_name'],
                    'endpoint' => $application['endpoint'],
                    'hit_count' => $application['hit_count'],
                ]);

                $index++;
            });
        });

        return new JsonResponse($rows, 200);
    }

    
    public function exportApplicationUsageCsv(Request $request)
    {
        $this->validate($request, [
            'fromDate' => 'sometimes|date',
            'toDate' => 'sometimes|date',
        ]);

        $fromDate = $request->fromDate;
        $toDate = $request->toDate;

        $csvContents = Cache::remember("application-usage-export-csv-{$fromDate}-{$toDate}", 600, function () use ($fromDate, $toDate) {
            return $this->client->exportApplicationUsageCsv($fromDate, $toDate);
        });

        $time = time();

        $filename = 'application-usage-' . $fromDate . '-' . $toDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return Response::make($csvContents, 200, $headers);
    }

    
    public function exportEndpointUsageCsv(Request $request)
    {
        $this->validate($request, [
            'fromDate' => 'sometimes|date',
            'toDate' => 'sometimes|date',
        ]);

        $fromDate = $request->fromDate;
        $toDate = $request->toDate;

        $csvContents = Cache::remember("application-usage-export-csv-{$fromDate}-{$toDate}", 600, function () use ($fromDate, $toDate) {
            return $this->client->exportEndpointUsageCsv($fromDate, $toDate);
        });

        $time = time();

        $filename = 'application-usage-' . $fromDate . '-' . $toDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return Response::make($csvContents, 200, $headers);
    }

    
    public function getTotals(Request $request): JsonResponse
    {
        $this->validate($request, [
            'society' => 'sometimes|string',
            'region' => 'sometimes|int',
            'hazard' => 'sometimes|string',
            'date' => 'sometimes|date',
            'language' => 'sometimes|string',
        ]);
        $response = $this->client->getTotals($request);

        return new JsonResponse($response, 200);
    }
}
