<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Http\Requests\HistoryListRequest;
use App\Http\Resources\WhatNow\HistoryResource;
use App\Models\History\History;
use App\Repositories\Access\UserRepository;
use App\Repositories\HistoryRepository;

class HistoryController extends Controller
{
    
    protected $history;

    
    protected $users;

    
    public function __construct(HistoryRepository $history, UserRepository $users)
    {
        $this->history = $history;
        $this->users = $users;
    }

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

    public function listForUser(int $id, HistoryListRequest $request)
    {
        $this->users->findOrFail($id);

        $this->authorize('list', History::class);

        $historyList = $this->history->getListForUser($request->getHistoryQuery(), $id);

        return HistoryResource::collection($historyList->paginate());
    }

    public function get($historyId)
    {
        $this->authorize('list', History::class);

        $history = $this->history->getById($historyId);

        return HistoryResource::make($history);
    }
}
