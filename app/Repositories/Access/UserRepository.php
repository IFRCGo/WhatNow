<?php

namespace App\Repositories\Access;

use App\Classes\UserQuery;
use App\Events\Backend\Auth\UserConfirmed;
use App\Exceptions\Auth\UserAlreadyConfirmedException;
use App\Exceptions\Auth\UserConfirmationException;
use App\Exceptions\Auth\UserConfirmationMismatchException;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use App\Repositories\Access\Role\RoleRepository;
use App\Repositories\Access\User\UserProfileRepository;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRepository extends Repository
{
    
    const MODEL = User::class;

    
    protected $userProfiles;

    
    protected $roles;

    
    public function __construct(UserProfileRepository $userProfiles, RoleRepository $roles)
    {
        $this->userProfiles = $userProfiles;
        $this->roles = $roles;
    }

    
    public function createUser($input): User
    {
        $userData = [
            'email' => $input['email'],
            'confirmation_code' => (string) UserConfirmationToken::generate(),
            'confirmed_role' => $input['confirmed_role'] ?? false,
        ];

        if (isset($input['password'])) {
            $userData['password'] = bcrypt($input['password']);
        } else {
            $userData['password'] = bcrypt(Str::random());
        }

        $user = new User($userData);

        DB::transaction(function () use ($user, $input) {

            
            $user->saveOrFail();
            $this->userProfiles->create($user->id, $input);

            if (isset($input['organisations'])) {
                $user->organisations()->delete();
                $orgs = collect($input['organisations'])->map(function ($org) {
                    return ['organisation_code' => $org];
                })->toArray();
                $user->organisations()->createMany($orgs);
            }

            if (isset($input['role_id'])) {

                
                $role = $this->roles->findOrFail($input['role_id']);
                $user->attachRoles([$role]);
                $user->save();
            }
        });

        if (isset($input['password'])) {
            $user->setPasswordUpdatedAt();
        }

        return $user;
    }

    public function updateUser(User $user, $input)
    {
        if (isset($input['email'])) {
            $this->update($user, [
                'email' => $input['email']
            ]);
        }

        $user->save();

        $this->userProfiles->update($user->userProfile, $input);

        if (isset($input['organisations'])) {
            $user->organisations()->delete();
            $orgs = collect($input['organisations'])->map(function ($org) {
                return ['organisation_code' => $org];
            })->toArray();
            $user->organisations()->createMany($orgs);
        }

        if (isset($input['role_id'])) {

            
            $role = $this->roles->findOrFail($input['role_id']);
            $user->detachRoles($user->roles);
            $user->attachRoles([$role]);
            $user->save();
        }

        return $user;
    }

    
    public function confirmAccount(UserConfirmationToken $token)
    {
        $user = $this->findByToken($token);

        if (!$user) {
            throw new UserConfirmationMismatchException();
        }

        if ($user->confirmed === 1) {
            throw new UserAlreadyConfirmedException();
        }

        if ($user->confirmation_code === (string) $token) {
            $user->confirmed = 1;

            event(new UserConfirmed($user));

            if (!parent::save($user)) {
                throw new UserConfirmationException();
            }

            return $user;
        }

        throw new UserConfirmationMismatchException();
    }

    
    public function updatePassword(User $user, $input)
    {
        $user->password = bcrypt($input['password']);

        if (parent::save($user)) {
            return true;
        }
    }

    
    public function findByToken(UserConfirmationToken $token)
    {
        return $this->query()->where('confirmation_code', (string) $token)->first();
    }

    
    public function findByEmail($email)
    {
        return $this->query()->where('email', (string)$email)->first();
    }

    
    public function queryUsers(UserQuery $userQuery): Builder
    {
        $builder = $this->query()->with(['roles', 'roles.permissions', 'userProfile', 'organisations']);

        $builder->join('user_profiles', 'users.id', '=', 'user_profiles.user_id');

        $builder->select('users.*');

        $userQuery->getFilters()->only(['activated', 'confirmed'])->each(function ($value, $column) use ($builder) {
            $builder->where($column, $value);
        });

        $builder->whereHas('userProfile', function ($query) use ($userQuery) {
            $userQuery->getFilters()->only(['industry_type', 'terms_version', 'country_code'])->each(function ($value, $column) use ($query) {
                $query->where($column, '=', $value);
            });

            $userQuery->getFilters()->only(['first_name', 'last_name',  'organisation'])->each(function ($value, $column) use ($query) {
                $query->where($column, 'like', "%$value%");
            });
        });

        $society = $userQuery->getFilters()->only(['society'])->first();
        if ($society) {
            $builder->whereHas('organisations', function ($query) use ($society) {
                $query->where('organisation_code', '=', $society);
            });
            $builder->orWhereHas('roles.permissions', function ($query) {
                $query->where('permissions.id', '=', 15);
            });
        }

        if ($userQuery->getFilters()->has('role')) {
            $roleId = $userQuery->getFilters()->get('role');

            $builder->whereHas('roles', function ($query) use ($roleId) {
                $query->where('roles.id', '=', (int) $roleId);
            });
        }

        if ($userQuery->hasPublicUsersExcluded()) {
            $builder->whereHas('roles', function (Builder $query) {
                $query->whereNotIn('roles.id', [Role::ROLE_DEFAULT]);
            });
        }

        if (in_array($userQuery->getOrderBy(), ['created_at', 'last_logged_in_at'])) {
            $builder->orderBy('users.'.$userQuery->getOrderBy(), $userQuery->getSort());
        }

        if (in_array($userQuery->getOrderBy(), ['first_name', 'last_name',  'organisation', 'industry_type'])) {
            $builder->orderByRaw('user_profiles.'.$userQuery->getOrderBy().' '.$userQuery->getSort());
        }

        return $builder;
    }

    public function deactivate(User $user)
    {
        $user->activated = false;
        $user->save();
    }

    public function reactivate(User $user)
    {
        $user->activated = true;
        $user->save();
    }

    public function allNotifiablePublicUsers()
    {
        return $this->query()->whereHas('roles', function ($query) {
            $query->where('all', '=', 0);
            $query->doesntHave('permissions');         })->whereHas('userProfile', function ($query) {
            $query->where('notifications_enabled', '=', 1);
        })->get();
    }
}
