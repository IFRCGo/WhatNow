<?php

namespace App\Http\Controllers\Auth;

use App\Events\Backend\Access\User\UserRegistered;
use App\Http\Controllers\Controller;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use App\Notifications\Auth\UserNeedsConfirmation;
use App\Repositories\Access\UserRepository;
use App\Repositories\TermsRepositoryInterface;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    
    private $users;

    
    private $terms;

    
    public function __construct(UserRepository $users, TermsRepositoryInterface $terms)
    {
        $this->middleware('guest');
        $this->users = $users;
        $this->terms = $terms;
    }

    
    protected function registered(Request $request, $user)
    {
        return $user;
    }

    
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:10|confirmed',
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'country_code' => 'required|string|max:3',
            'organisation' => 'required|string|max:191',
            'industry_type' => 'required|string|max:191',
            'api_used_in' => 'nullable|string|max:255',
        ]);
    }

    
    protected function create(array $data)
    {
                $data['role_id'] = Role::ROLE_DEFAULT;

                $data['terms_version'] = $this->terms->getLatestTermsVersion();

        $user = $this->users->createUser($data);

        if (! $user->isConfirmed()) {
            $user->notify(new UserNeedsConfirmation(new UserConfirmationToken($user->confirmation_code)));
        }

        event(new UserRegistered($user));

        return $user;
    }
}
