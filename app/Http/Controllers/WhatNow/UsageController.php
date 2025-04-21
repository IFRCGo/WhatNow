<?php

namespace App\Http\Controllers\WhatNow;

use App\Classes\RcnApi\RcnApiClient;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
/**
 * @OA\Tag(
 *     name="Usage",
 *     description="Operations about usage of endpoints"
 * )
 */
final class UsageController extends ApiController
{

    private $client;


    public function __construct(RcnApiClient $client)
    {
        $this->client = $client->usage();
    }

    /**
     * @OA\Get(
     *     path="/usage/applications",
     *     tags={"Usage"},
     *     summary="Obtiene el conteo de solicitudes de aplicaciones",
     *     description="Retorna una lista de solicitudes de aplicaciones dentro de un rango de fechas específico.",
     *     operationId="listApplicationRequestCount",
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="Fecha de inicio para el filtro de solicitudes",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="Fecha de fin para el filtro de solicitudes",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página para la paginación",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
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

    /**
     * @OA\Post(
     *     path="/usage/endpoints",
     *     tags={"Usage"},
     *     summary="Obtiene el conteo de solicitudes de endpoints",
     *     description="Retorna una lista de solicitudes de endpoints dentro de un rango de fechas específico.",
     *     operationId="listEndpointRequestCount",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para filtrar las solicitudes de endpoints",
     *         @OA\JsonContent(
     *             required={"fromDate", "toDate"},
     *             @OA\Property(property="fromDate", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="toDate", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="page", type="integer", example=1)
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

    /**
     * @OA\Post(
     *     path="/usage/export/applications",
     *     tags={"Usage"},
     *     summary="Exporta el uso de aplicaciones en formato CSV",
     *     description="Genera y devuelve un archivo CSV con el uso de aplicaciones dentro de un rango de fechas específico.",
     *     operationId="exportApplicationUsageCsv",
     *     @OA\RequestBody(
     *         required=false,
     *         description="Datos para filtrar la exportación de uso de aplicaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="fromDate", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="toDate", type="string", format="date", example="2023-12-31")
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

    /**
     * @OA\Post(
     *     path="/usage/export/endpoints",
     *     tags={"Usage"},
     *     summary="Exporta el uso de endpoints en formato CSV",
     *     description="Genera y devuelve un archivo CSV con el uso de endpoints dentro de un rango de fechas específico.",
     *     operationId="exportEndpointUsageCsv",
     *     @OA\RequestBody(
     *         required=false,
     *         description="Datos para filtrar la exportación de uso de endpoints",
     *         @OA\JsonContent(
     *             @OA\Property(property="fromDate", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="toDate", type="string", format="date", example="2023-12-31")
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

    /**
     * @OA\Get(
     *     path="/usage/totals",
     *     tags={"Usage"},
     *     summary="Obtiene los totales de uso",
     *     description="Endpoint para obtener los totales de uso basados en los filtros proporcionados.",
     *     operationId="getTotals",
     *     @OA\RequestBody(
     *         description="Parámetros opcionales para filtrar los resultados",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="society", type="string", example="Ejemplo Sociedad"),
     *             @OA\Property(property="subnationals", type="integer", example=1),
     *             @OA\Property(property="hazard", type="string", example="Ejemplo Peligro"),
     *             @OA\Property(property="date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="language", type="string", example="es")
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
