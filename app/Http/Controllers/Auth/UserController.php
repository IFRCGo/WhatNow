<?php

namespace App\Http\Controllers\Auth;

use App\Events\Backend\Access\User\UserCreated;
use App\Events\Backend\Access\User\UserDeactivated;
use App\Events\Backend\Access\User\UserDeleted;
use App\Events\Backend\Access\User\UserUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserListRequest;
use App\Http\Resources\UserResource;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use App\Notifications\Auth\AdminUserNeedsConfirmation;
use App\Repositories\Access\Role\RoleRepository;
use App\Repositories\Access\UserRepository;
use App\Repositories\TermsRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
    
    private $users;

    
    private $roles;

    
    private $terms;

    
    public function __construct(UserRepository $users, RoleRepository $roles, TermsRepository $terms)
    {
        $this->middleware('auth:api');
        $this->users = $users;
        $this->roles = $roles;
        $this->terms = $terms;
    }

    
    protected function create(Request $request)
    {
        $this->authorize('create', User::class);
        $this->validate($request, [
            'email' => 'required|email|max:191|unique:users',
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'country_code' => 'required|string|max:2',
            'organisation' => 'string|max:191',
            'industry_type' => 'string|max:191',
            'api_used_in' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

        $latestTermsVersion = $this->terms->getLatestTermsVersion();
        if (! $$latestTermsVersion) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'No terms and conditions found');
        }

        $adminUser = $request->user();
        
        $adminRole = $adminUser->roles->first();
        
        $role = $this->roles->findOrFail($request->get('role_id'));

        $this->checkRoleCanBeAssigned($adminRole, $role);

        $user = $this->users->createUser(array_merge($request->all(), [
            'terms_version' => $latestTermsVersion,
            'accepted_agreement' => true,
        ]));


        $user->notify(new AdminUserNeedsConfirmation(new UserConfirmationToken($user->confirmation_code)));

        event(new UserCreated($user));

        return $user;
    }

    
    private function checkRoleCanBeAssigned(Role $adminRole, Role $roleToBeAssigned, Role $currentRole = null)
    {
        if (! $adminRole->hasAll()) {
            
            
            $permissions = $roleToBeAssigned->permissions;
            $diff = $permissions->diff($adminRole->permissions);
            if ($diff->isNotEmpty()) {
                throw new  HttpException(Response::HTTP_FORBIDDEN, 'You cannot assign a user a role higher than your own.');
            }

            if ($currentRole) {
                
                
                $permissions = $currentRole->permissions;
                $diff = $permissions->diff($adminRole->permissions);

                if ($diff->isNotEmpty()) {
                    throw new  HttpException(Response::HTTP_FORBIDDEN, 'You cannot change the permissions of a user with a role higher than your own.');
                }
            }
        }
    }

    
    public function list(UserListRequest $request)
    {
        $this->authorize('list', User::class);

        $users = $this->users->queryUsers($request->getUserQuery());

        return UserResource::collection($users->paginate());
    }

    
    public function listAdmins(UserListRequest $request)
    {
        $this->authorize('list', User::class);

        $userQuery = $request->getUserQuery();
        $userQuery->excludePublicUsers();

        $users = $this->users->queryUsers($userQuery);

        return UserResource::collection($users->paginate());
    }

    
    public function view(Request $request, int $userId)
    {
        $user = $this->users->findOrFail($userId, ['organisations', 'roles', 'roles.permissions']);

        $this->authorize('view', $user);

        return UserResource::make($user);
    }

    
    public function me(Request $request)
    {
        $user = $this->users->findOrFail($request->user()->id, ['organisations', 'roles', 'roles.permissions']);

        $this->authorize('view', $user);

        return UserResource::make($user);
    }

    
    public function update(Request $request, int $userId)
    {
        
        $user = $this->users->findOrFail($userId);

        $this->authorize('update', $user);

        $this->validate($request, [
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'required_with:email',
            'first_name' => 'string|max:191',
            'last_name' => 'string|max:191',
            'country_code' => 'string|max:2',
            'organisation' => 'string|max:191',
            'industry_type' => 'string|max:191',
            'terms_version' => 'string|max:10',
            'accepted_agreement' => 'boolean',
            'api_used_in' => 'nullable|string|max:255',
            'role_id' => 'exists:roles,id',
            'organisations' => 'array',
            'organisations.*' => 'string|distinct|min:3|max:3',
            'confirmed_role' => 'nullable|boolean',
        ]);

        
        $adminUser = $request->user();

        if (! is_null($request->get('role_id')) && ! $adminUser->hasAll()) {
            if ($adminUser->id === $user->id) {
                throw new HttpException(Response::HTTP_FORBIDDEN, 'You cannot change your own role');
            }

            
            $adminRole = $adminUser->roles->first();
            
            $role = $this->roles->findOrFail($request->get('role_id'));
            
            $currentRole = $user->roles->first();
            $this->checkRoleCanBeAssigned($adminRole, $role, $currentRole);
            $user->confirmed_role = false;
        }

        if ($request->get('email') && ! Hash::check($request->get('password'), $user->password)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Password is not valid');
        }

        if ($request->accepted_agreement) {
            $termsVersion = $this->terms->getLatestTermsVersion();
        }

        if ($request->confirmed_role) {
            $user->confirmed_role = true;
        }

        $input = array_merge($request->except('password'), [
            'terms_version' => $termsVersion ?? null,
        ]);

        $user = $this->users->updateUser($user, $input);
        $user->load('organisations');

        event(new UserUpdated($user));


        return UserResource::make($user);
    }

    
    public function delete(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('delete', $user);

        $this->users->delete($user);

        event(new UserDeleted($user));

        return new JsonResponse(['message' => 'User deleted'], Response::HTTP_OK);
    }

    
    public function deactivate(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('deactivate', $user);

        $this->users->deactivate($user);

        event(new UserDeactivated($user));

        return new JsonResponse(['message' => 'User deactivated'], Response::HTTP_OK);
    }

    
    public function reactivate(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('reactivate', $user);

        $this->users->reactivate($user);

        event(new UserDeactivated($user));

        return new JsonResponse(['message' => 'User reactivated'], Response::HTTP_OK);
    }
}
