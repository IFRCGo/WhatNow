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
/**
 * @OA\Tag(
 *     name="Users",
 *     description="Operations about users"
 * )
 */
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

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Crear un nuevo usuario",
     *     description="Crea un nuevo usuario con la información proporcionada.",
     *     operationId="createUser",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del usuario a crear",
     *         @OA\JsonContent(
     *             required={"email", "first_name", "last_name", "country_code", "role_id"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="country_code", type="string", example="US"),
     *             @OA\Property(property="organisation", type="string", example="Acme Corp"),
     *             @OA\Property(property="industry_type", type="string", example="Technology"),
     *             @OA\Property(property="api_used_in", type="string", example="Internal API"),
     *             @OA\Property(property="role_id", type="integer", example=1)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
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
        if (!$latestTermsVersion) {
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

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="List all users",
     *     description="Returns a list of all users",
     *     operationId="listUsers",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function list(UserListRequest $request)
    {
        $this->authorize('list', User::class);

        $users = $this->users->queryUsers($request->getUserQuery());

        return UserResource::collection($users->paginate());
    }

    
    /**
     * @OA\Get(
     *     path="/users/admins",
     *     tags={"Users"},
     *     summary="List all admin users",
     *     description="Returns a list of all admin users",
     *     operationId="listAdmins",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function listAdmins(UserListRequest $request)
    {
        $this->authorize('list', User::class);

        $userQuery = $request->getUserQuery();
        $userQuery->excludePublicUsers();

        $users = $this->users->queryUsers($userQuery);

        return UserResource::collection($users->paginate());
    }

    
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Get a specific user by ID",
     *     description="Returns details of a specific user identified by their ID",
     *     operationId="viewUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the user to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function view(Request $request, int $userId)
    {
        $user = $this->users->findOrFail($userId, ['organisations', 'roles', 'roles.permissions']);

        $this->authorize('view', $user);

        return UserResource::make($user);
    }

    
    /**
     * @OA\Get(
     *     path="/users/me",
     *     tags={"Users"},
     *     summary="Obtener la información del usuario autenticado",
     *     description="Retorna la información del usuario autenticado, incluyendo sus organizaciones, roles y permisos.",
     *     operationId="getAuthenticatedUser",
     *     security={{"bearerAuth": {}}},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function me(Request $request)
    {
        $user = $this->users->findOrFail($request->user()->id, ['organisations', 'roles', 'roles.permissions']);

        $this->authorize('view', $user);

        return UserResource::make($user);
    }

    /**
     * @OA\Patch(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Update user information",
     *     description="Updates a user's details with the provided information",
     *     operationId="updateUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="first_name", type="string", maxLength=191),
     *             @OA\Property(property="last_name", type="string", maxLength=191),
     *             @OA\Property(property="country_code", type="string", maxLength=2),
     *             @OA\Property(property="organisation", type="string", maxLength=191),
     *             @OA\Property(property="industry_type", type="string", maxLength=191),
     *             @OA\Property(property="terms_version", type="string", maxLength=10),
     *             @OA\Property(property="accepted_agreement", type="boolean"),
     *             @OA\Property(property="api_used_in", type="string", maxLength=255, nullable=true),
     *             @OA\Property(property="role_id", type="integer"),
     *             @OA\Property(
     *                 property="organisations",
     *                 type="array",
     *                 @OA\Items(type="string", minLength=3, maxLength=3)
     *             ),
     *             @OA\Property(property="confirmed_role", type="boolean", nullable=true)
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
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

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user",
     *     description="Deletes a user by their ID",
     *     operationId="deleteUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
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
    public function delete(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('delete', $user);

        $this->users->delete($user);

        event(new UserDeleted($user));

        return new JsonResponse(['message' => 'User deleted'], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/users/{id}/deactivate",
     *     tags={"Users"},
     *     summary="Deactivate a user",
     *     description="Deactivates an active user",
     *     operationId="deactivateUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
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
    public function deactivate(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('deactivate', $user);

        $this->users->deactivate($user);

        event(new UserDeactivated($user));

        return new JsonResponse(['message' => 'User deactivated'], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/users/{id}/reactivate",
     *     tags={"Users"},
     *     summary="Reactivate a user",
     *     description="Reactivates a deactivated user",
     *     operationId="reactivateUser",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
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
    public function reactivate(int $userId)
    {
        $user = $this->users->findOrFail($userId);

        $this->authorize('reactivate', $user);

        $this->users->reactivate($user);

        event(new UserDeactivated($user));

        return new JsonResponse(['message' => 'User reactivated'], Response::HTTP_OK);
    }
}
