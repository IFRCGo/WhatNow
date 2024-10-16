<?php

namespace App\Http\Controllers\Auth;

use App\Classes\Auth\Facebook\FacebookUserService;
use App\Classes\Auth\Google\GoogleUserService;
use App\Http\Controllers\ApiController;
use App\Models\Access\User\User;
use App\Models\OAuthProvider;
use App\Models\Access\Role\Role;
use App\Repositories\Access\UserRepository;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use App\Events\Backend\Auth\UserLoggedIn;

class OAuthController extends ApiController
{
    use AuthenticatesUsers;

    
    protected $users;

    
    protected $facebookUserService;

    
    protected $googleUserService;

    
    public function __construct(
        UserRepository $users,
        FacebookUserService $facebookUserService,
        GoogleUserService $googleUserService
    ) {
        $this->users = $users;
        $this->facebookUserService = $facebookUserService;
        $this->googleUserService = $googleUserService;
    }

    public function login(Request $request)
    {
        if (!in_array($request->driver, ['facebook', 'google'])) {
            $this->respondWithNotFound(new \Exception('Provider not found'));
        }

        $this->validate($request, [
            'token' => 'required|string'
        ]);

        
        $socialUser = $this->{$request->driver . 'UserService'}->getUserFromToken($request->get('token'));

        if ($provider = $this->findProvider($request->driver, $socialUser->getId())) {
            $provider->update([
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
            ]);
            $user = $provider->user;
        } else {
                        if ($user = $this->users->findByEmail($socialUser->getEmail())) {
                $this->mergeWithExistingUser($request->driver, $user, $socialUser);
            } else {
                                $user = $this->createUser($request->driver, $socialUser);
            }
        }

                $token = $this->guard()->login($user);
        $this->guard()->setToken($token);

        return $this->sendLoginResponse($request);
    }


    
    protected function sendLoginResponse(Request $request)
    {
        $this->authenticated($request, $this->guard()->user());

        $this->clearLoginAttempts($request);

        $token = (string)$this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
        ];
    }

    
    protected function createUser($driver, $sUser)
    {
                $name = $sUser->getName();
        $nameParts = explode(' ', $name);
        $firstName = array_shift($nameParts);
        $lastName = implode('', $nameParts);

        $user = $this->users->createUser([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $sUser->getEmail(),
            'role_id' => Role::ROLE_DEFAULT
        ]);

        $user->confirm();

        $user->oauthProviders()->create([
            'provider' => $driver,
            'provider_user_id' => $sUser->getId(),
            'access_token' => $sUser->token,
            'refresh_token' => $sUser->refreshToken,
        ]);

        return $user;
    }

    
    protected function findProvider($driver, $userId)
    {
        return OAuthProvider::where('provider', $driver)
            ->where('provider_user_id', $userId)
            ->first();
    }

    
    private function mergeWithExistingUser(string $provider, User $user, \Laravel\Socialite\Contracts\User $sUser)
    {
        $user->oauthProviders()->create([
            'provider' => $provider,
            'provider_user_id' => $sUser->getId(),
            'access_token' => $sUser->token,
            'refresh_token' => $sUser->refreshToken,
        ]);

        return $user;
    }

    
    protected function authenticated(Request $request, Authenticatable $user)
    {
        if ($user instanceof User) {
            if (!$user->isActive()) {
                $this->guard()->logout();
                throw ValidationException::withMessages([
                    $this->username() => [trans('auth.deactivated', ['support_email' => config('mail.support_email')])],
                ]);
            }
        }

        event(new UserLoggedIn($user));
    }
}
