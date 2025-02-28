<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryListRequest;
use App\Http\Resources\WhatNow\HistoryResource;
use App\Models\History\History;
use App\Repositories\Access\UserRepository;
use App\Repositories\HistoryRepository;

/**
 * @OA\Tag(
 *     name="Historial",
 *     description="Operations about historial"
 * )
 */
class HistoryController extends Controller
{
    
    protected $history;

    
    protected $users;

    
    public function __construct(HistoryRepository $history, UserRepository $users)
    {
        $this->history = $history;
        $this->users = $users;
    }

    /**
     * @OA\Get(
     *     path="/history",
     *     tags={"Historial"},
     *     summary="List application history",
     *     description="Retrieves the history of application actions",
     *     operationId="listApplicationHistory",
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
    public function list(HistoryListRequest $request)
    {
        $this->authorize('list', History::class);

        $user = $request->user();

        $historyList = $this->history->getList($request->getHistoryQuery());

        if (! $user->hasPermission('organisations_all')) {
                        $assignedCountryCodes = $request->user()->organisations->pluck('organisation_code')->toArray();
            $historyList->whereIn('country_code', $assignedCountryCodes);
        }

        return HistoryResource::collection($historyList->paginate());
    }

    /**
     * @OA\Get(
     *     path="/users/{id}/history",
     *     tags={"Historial"},
     *     summary="Get history for a specific user",
     *     description="Retrieves the history of actions performed by a specific user",
     *     operationId="listUserHistory",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user whose history is being retrieved",
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
    public function listForUser(int $id, HistoryListRequest $request)
    {
        $this->users->findOrFail($id);

        $this->authorize('list', History::class);

        $historyList = $this->history->getListForUser($request->getHistoryQuery(), $id);

        return HistoryResource::collection($historyList->paginate());
    }

    /**
     * @OA\Get(
     *     path="/history/{id}",
     *     tags={"Historial"},
     *     summary="Get history entry by ID",
     *     description="Retrieves a specific history entry by its ID",
     *     operationId="getHistoryEntry",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the history entry to retrieve",
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
    public function get($historyId)
    {
        $this->authorize('list', History::class);

        $history = $this->history->getById($historyId);

        return HistoryResource::make($history);
    }
}
