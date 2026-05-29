<?php

namespace App\Http\Requests;

use App\Classes\UserQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

class UserListRequest extends FormRequest
{
    
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        return [
            'orderBy' => 'in:first_name,last_name,organisation,industry_type,created_at,last_logged_in_at',
            'sort' => 'in:asc,desc'
        ];
    }

    
    public function getUserQuery(): UserQuery
    {
        $orderBy = $this->get('orderBy', 'created_at');
        $sort = $this->get('sort', 'asc');

        $userQuery = new UserQuery();

        $userQuery->setOrderBy($orderBy);
        $userQuery->setSort($sort);

        foreach ($this->get('filters', []) as $column => $value) {
            $userQuery->addFilter($column, $value);
        }

        return $userQuery;
    }
}
