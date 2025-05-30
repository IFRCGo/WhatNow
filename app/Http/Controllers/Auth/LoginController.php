<?php

namespace App\Http\Controllers\Auth;

use App\Events\Backend\Auth\UserLoggedIn;
use App\Models\Access\User\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Operations about authentication"
 * )
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Login to get JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="email@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your-jwt-token")
     *         )
     *     )
     * )
     */
    //login from AuthenticatesUsers trait
    
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if ($token) {
            $this->guard()->setToken($token);

            return true;
        }

        return false;
    }

    
    protected function sendLoginResponse(Request $request)
    {
        $this->authenticated($request, $this->guard()->user());

        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ];
    }

    
    public function logout(Request $request)
    {
        $this->guard()->logout();
    }

    
    protected function authenticated(Request $request, Authenticatable $user)
    {
        if($user instanceof User) {
            if (!$user->isConfirmed()) {
                auth()->logout();
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.confirmation.resend')],
                ]);
            }
            if (!$user->isActive()) {
                auth()->logout();
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.deactivated', ['support_email' => config('mail.support_email')])],
                ]);
            }
        }

        event(new UserLoggedIn($user));
    }
}
