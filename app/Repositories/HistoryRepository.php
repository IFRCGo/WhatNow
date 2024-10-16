<?php

namespace App\Repositories;

use App\Classes\HistoryQuery;
use App\Models\History\History;
use Illuminate\Support\Facades\Auth;

class HistoryRepository extends Repository
{
    
    const MODEL = History::class;

    
    public function getById($historyId)
    {
        return (self::MODEL)::findOrFail($historyId);
    }

    
    public function getList(HistoryQuery $historyQuery)
    {
        $builder = $this->query()->with('user');

        foreach ($historyQuery->getFilters() as $key => $filter)
        {
            $builder->where($key, '=', $filter);
        }

        $builder->orderBy($historyQuery->getOrderBy(), $historyQuery->getSort());
        return $builder;
    }

    
    public function getListForUser(HistoryQuery $historyQuery, int $userId)
    {
        $builder = $this->query()->where('user_id', '=', $userId)->with('user');

        foreach ($historyQuery->getFilters() as $key => $filter)
        {
            $builder->where($key, '=', $filter);
        }

        $builder->orderBy($historyQuery->getOrderBy(), $historyQuery->getSort());
        return $builder;
    }

    
    public function getListForSociety(HistoryQuery $historyQuery, int $userId)
    {
        $builder = $this->query()->where('country_code', '=', $userId)->with('user');
        $builder->orderBy($historyQuery->getOrderBy(), $historyQuery->getSort());
        return $builder;
    }

    
    public static function create(string $action, string $contentName, $entityId = null, $countryCode = null, $languageCode = null)
    {
        $history = (self::MODEL)::create([
            'country_code' => $countryCode,
            'language_code' => $languageCode,
            'entity_id' => $entityId,
            'action' => $action,
            'user_id' => Auth::id(),
            'content' => $contentName
        ]);

        $history->save();
    }
}
