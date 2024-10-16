<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventTypeCreateRequest extends FormRequest
{
    
    public function authorize()
    {
        $user = request()->user();
        return !empty($user) && ( $user->hasAll() or $user->hasPermission('hz-type-create'));
    }

    
    public function rules()
    {
        return [
            'name' => ['required'],
            'icon' => ['required', 'mimes:png', 'max:30']
        ];
    }
}
