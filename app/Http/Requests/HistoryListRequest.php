<?php

namespace App\Http\Requests;

use App\Classes\HistoryQuery;
use Illuminate\Foundation\Http\FormRequest;

class HistoryListRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
            'orderBy' => 'in:created_at,content,language_code,country_code',
            'sort' => 'in:asc,desc'
        ];
    }

    
    public function getHistoryQuery(): HistoryQuery
    {
        $orderBy = $this->get('orderBy', 'created_at');
        $sort = $this->get('sort', 'asc');

        $historyQuery = new HistoryQuery();

        $historyQuery->setOrderBy($orderBy);
        $historyQuery->setSort($sort);

        $filters = $this->get('filters', []);
        foreach ($filters as $column => $value) {
            $historyQuery->addFilter($column, $value);
        }

        $countryCode = $this->get('countryCode', null);
        if(!is_null($countryCode) && !isset($filters['country_code']))
        {
            $historyQuery->addFilter('country_code', $countryCode);
        }

        return $historyQuery;
    }
}
