<?php

namespace App\Http\Controllers\WhatNow;

use OpenApi\Attributes as OA;

use App\Classes\RcnApi\RcnApiClient;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
#[OA\Tag(
    name: 'Usage',
    description: 'Operations about usage of endpoints',
)]
final class UsageController extends ApiController
{

    private $client;


    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->usage();
    }

    #[OA\Get(
        path: '/usage/applications',
        tags: ['Usage'],
        summary: 'Get application request counts',
        description: 'Return a list of application requests within the specified date range.',
        operationId: 'listApplicationRequestCount',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'fromDate',
            in: 'query',
            description: 'Start date for filtering requests',
            required: true,
            schema: new OA\Schema(
                type: 'string',
                format: 'date',
            ),
        ), new OA\Parameter(
            name: 'toDate',
            in: 'query',
            description: 'End date for filtering requests',
            required: true,
            schema: new OA\Schema(
                type: 'string',
                format: 'date',
            ),
        ), new OA\Parameter(
            name: 'page',
            in: 'query',
            description: 'Page number for pagination',
            required: false,
            schema: new OA\Schema(
                type: 'integer',
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
    public function listApplicationRequestCount(Request $request): JsonResponse
    {
        $this->validate($request, [
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'page' => 'sometimes|integer',
            'orderBy' => 'sometimes|string',
            'sort' => 'sometimes|string|in:asc,desc',
        ]);

        $response = $this->client->listApplicationRequestCount($request->fromDate, $request->toDate, $request->page ?? 1, false, $request->orderBy,  $request->sort);

        return new JsonResponse($response, 200);
    }

    #[OA\Post(
        path: '/usage/endpoints',
        tags: ['Usage'],
        summary: 'Get endpoint request counts',
        description: 'Return a list of endpoint requests within the specified date range.',
        operationId: 'listEndpointRequestCount',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Filters for endpoint requests',
            content: new OA\JsonContent(
                required: ['fromDate', 'toDate'],
                properties: [new OA\Property(
                    property: 'fromDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-01-01',
                ), new OA\Property(
                    property: 'toDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-12-31',
                ), new OA\Property(
                    property: 'page',
                    type: 'integer',
                    example: 1,
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

    #[OA\Post(
        path: '/usage/export/applications',
        tags: ['Usage'],
        summary: 'Exporta el uso de aplicaciones en formato CSV',
        description: 'Generate and return a CSV file with application usage within the specified date range.',
        operationId: 'exportApplicationUsageCsv',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            description: 'Filters for the application usage export',
            content: new OA\JsonContent(
                properties: [new OA\Property(
                    property: 'fromDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-01-01',
                ), new OA\Property(
                    property: 'toDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-12-31',
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

    #[OA\Post(
        path: '/usage/export/endpoints',
        tags: ['Usage'],
        summary: 'Exporta el uso de endpoints en formato CSV',
        description: 'Generate and return a CSV file with endpoint usage within the specified date range.',
        operationId: 'exportEndpointUsageCsv',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            description: 'Filters for the endpoint usage export',
            content: new OA\JsonContent(
                properties: [new OA\Property(
                    property: 'fromDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-01-01',
                ), new OA\Property(
                    property: 'toDate',
                    type: 'string',
                    format: 'date',
                    example: '2023-12-31',
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

    #[OA\Get(
        path: '/usage/totals',
        tags: ['Usage'],
        summary: 'Get usage totals',
        description: 'Get usage totals based on the provided filters.',
        operationId: 'getTotals',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            description: 'Optional parameters for filtering results',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'society',
                    type: 'string',
                    example: 'Ejemplo Sociedad',
                ), new OA\Property(
                    property: 'subnationals',
                    type: 'integer',
                    example: 1,
                ), new OA\Property(
                    property: 'hazard',
                    type: 'string',
                    example: 'Ejemplo Peligro',
                ), new OA\Property(
                    property: 'date',
                    type: 'string',
                    format: 'date',
                    example: '2023-01-01',
                ), new OA\Property(
                    property: 'language',
                    type: 'string',
                    example: 'es',
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
    public function getTotals(Request $request): JsonResponse
    {
        $this->validate($request, [
            'society' => 'sometimes|string',
            'subnationals' => 'sometimes|int',
            'hazard' => 'sometimes|string',
            'date' => 'sometimes|date',
            'language' => 'sometimes|string',
        ]);
        $response = $this->client->getTotals($request);

        return new JsonResponse($response, 200);
    }
}
