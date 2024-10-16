<?php

namespace App\Http\Controllers\Settings;

use App\Repositories\Access\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;

class PasswordController extends ApiController
{

    
    public function update(Request $request, UserRepository $userRepo)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:10'
        ]);

        $password = $this->guard()->validate(
            [
                'email' => $request->user()->email,
                'password' => $request->input('current_password')
            ]
        );

        if(!$password) {
            return new JsonResponse(['message' => 'Forbidden'], JsonResponse::HTTP_FORBIDDEN);
        }

        $userRepo->updatePassword($request->user(), $request->input());

                $token = $this->guard()->refresh(true, true);

        event(new PasswordReset($request->user()));

        return new JsonResponse([
            'token' => $token,
            'token_type' => 'bearer'
        ], JsonResponse::HTTP_OK);
    }

    
    protected function guard()
    {
        return Auth::guard('api');
    }

}
