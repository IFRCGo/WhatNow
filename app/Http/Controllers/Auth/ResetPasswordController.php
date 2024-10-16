<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:10',
        ];
    }

    
    public function __construct()
    {
        $this->middleware('guest');
    }

    
    protected function sendResetResponse(Request $request, $response)
    {
        return ['status' => trans($response)];
    }

    
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], 400);
    }
}
