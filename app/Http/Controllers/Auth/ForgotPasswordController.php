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
        $this->middleware('guest')->except('sendResetLinkEmail');
    }

    /**
     * @OA\Post(
     *     path="/password/email",
     *     tags={"Auth"},
     *     summary="Send a password reset link",
     *     description="Sends a password reset link to the given email address",
     *     operationId="sendResetLinkEmail",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", description="The user's email address", example="user@example.com")
     *         )
     *     ),
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
    //sendResetLinkEmail from SendsPasswordResetEmails trait


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
