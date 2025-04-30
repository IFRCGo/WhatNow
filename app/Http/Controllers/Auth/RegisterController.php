<?php

namespace App\Http\Controllers\Auth;

use App\Classes\MailApi\MailApiService;
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
    private $mailApiService;


    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Registers a new user and returns a successful response",
     *     operationId="register",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="Securepassword123*"),
     *             @OA\Property(property="password_confirmation", type="string", example="Securepassword123*"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="country_code", type="string", example="US"),
     *             @OA\Property(property="organisation", type="string", example="Company Name"),
     *             @OA\Property(property="industry_type", type="string", example="Technology"),
     *             @OA\Property(property="api_used_in", type="string")
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
    //register from RegistersUsers trait

    
    public function __construct(UserRepository $users, TermsRepositoryInterface $terms, MailApiService $mailApiService)
    {
        $this->middleware('guest');
        $this->users = $users;
        $this->terms = $terms;
        $this->mailApiService = $mailApiService;
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
        ]);
    }

    
    protected function create(array $data)
    {
                $data['role_id'] = Role::ROLE_DEFAULT;

                $data['terms_version'] = $this->terms->getLatestTermsVersion();

        $user = $this->users->createUser($data);

        if (! $user->isConfirmed()) {
            $notification = new UserNeedsConfirmation(new UserConfirmationToken($user->confirmation_code), $this->mailApiService);
            $notification->toMail($user);
        }

        event(new UserRegistered($user));

        return $user;
    }
}
