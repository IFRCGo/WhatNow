<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return ['status' => __($response)];
    }

    
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
                if ($response === Password::INVALID_USER) {
            return response()->json(['status' => __(Password::RESET_LINK_SENT)], Response::HTTP_OK);
        }

        return response()->json(['email' => __($response)], Response::HTTP_BAD_REQUEST);
    }
}
