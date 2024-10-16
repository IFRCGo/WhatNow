<?php

namespace Tests\Feature;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /** @var \App\Models\Access\User\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user->userProfile()->save(factory(UserProfile::class)->make());
    }

    /** @test */
    public function update_profile_info()
    {
        $this->actingAs($this->user)
            ->patchJson('/api/users/'.$this->user->id, [
                'first_name' => 'Joe',
                'last_name' => 'Bloggs'
            ])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'user_profile' => [
                        'first_name' => 'Joe',
                        'last_name' => 'Bloggs'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'first_name' => 'Joe',
            'last_name' => 'Bloggs'
        ]);
    }

    /** @test */
    public function updating_email_without_password()
    {
        $this->actingAs($this->user)
            ->patchJson('/api/users/'.$this->user->id, [
                'email' => 'test@test.com',
            ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);


        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function updating_email_with_password()
    {
        Mail::fake();
        $this->user->password = Hash::make('1234');
        $this->user->save();

        $this->actingAs($this->user)
            ->patchJson('/api/users/'.$this->user->id, [
                'email' => 'test@test.com',
                'password' => '1234'
            ])
            ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => 'test@test.com'
        ]);
    }


    /** @test */
    public function updating_email_with_wrong_password()
    {
        $this->user->password = Hash::make('1234');
        $this->user->save();

        $this->actingAs($this->user)
            ->patchJson('/api/users/'.$this->user->id, [
                'email' => 'test@test.com',
                'password' => '6789'
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function admin_updating_another_user_profile()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'first_name' => 'Joe',
                'last_name' => 'Bloggs'
            ])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'user_profile' => [
                        'first_name' => 'Joe',
                        'last_name' => 'Bloggs'
                    ]
                ]
            ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'first_name' => 'Joe',
            'last_name' => 'Bloggs'
        ]);
    }

    /** @test */
    public function admin_updating_another_users_email_without_password()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'email' => 'test@test.com',
            ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);


        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => $this->user->email
        ]);
    }

    /** @test */
    public function admin_promoting_users_role_to_higher_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Super Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
                Permission::where('name', '=', 'content-publish')->first()->id
            ]);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function admin_promoting_users_role_to_equal_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Super Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
            ]);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertSuccessful();
    }


    /** @test */
    public function admin_promoting_users_role_to_lesser_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Super Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
            ]);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertSuccessful();
    }

    /** @test */
    public function admin_demoting_users_role_from_a_higher_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
                Permission::where('name', '=', 'content-publish')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Other Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
            ]);

        $existingRole = Role::create(['name' => 'Super Admin']);
        $existingRole->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
                Permission::where('name', '=', 'content-publish')->first()->id,
                Permission::where('name', '=', 'content-edit')->first()->id
            ]);

        $this->user->roles()->sync([]);
        $this->user->roles()->attach($existingRole->id);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function admin_demoting_users_role_form_an_equal_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);
        $this->user->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Other Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id,
            ]);

        $this->user->roles()->sync([]);
        $this->user->roles()->attach($roleToAssign->id);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertSuccessful();
    }


    /** @test */
    public function admin_demoting_users_role_from_a_lesser_role_than_your_own()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
                Permission::where('name', '=', 'users-edit')->first()->id
            ]);
        $adminUser->roles()->attach($role->id);

        $roleToAssign = Role::create(['name' => 'Other Admin']);
        $roleToAssign->permissions()
            ->sync([
                Permission::where('name', '=', 'view-backend')->first()->id,
            ]);

        $this->user->roles()->sync([]);
        $this->user->roles()->attach($roleToAssign);

        $this->actingAs($adminUser)
            ->patchJson('/api/users/'.$this->user->id, [
                'role_id' => $roleToAssign->id,
            ])
            ->assertSuccessful();
    }

    /** @test */
    public function admin_delete_user()
    {
        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser);
        $this->deleteJson('/api/users/'.$this->user->id)
            ->assertSuccessful();
    }
}
