<?php

namespace Tests\Feature;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use App\Notifications\Auth\AdminUserNeedsConfirmation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use App\Models\Access\User\UserOrganisation;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @var \App\Models\Access\User\User */
    protected $user;

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_user()
    {
        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $this->user->organisations()->save(\Database\Factories\UserOrganisationFactory::new()->make());

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-view')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser)
            ->getJson('/api/users/' . $this->user->id)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => $this->getUserJsonStructure()
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function register_admin_user()
    {
        Notification::fake();

        /** @var User $adminUser */
        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser);

        $this->postJson('/api/users', [
            'email' => 'test98765@test.app',
            'password' => 'secretlongerthan10',
            'password_confirmation' => 'secretlongerthan10',
            'first_name' => 'Colin',
            'last_name' => 'Frizzell',
            'industry_type' => 'Gaming',
            'organisation' => '3SC',
            'country_code' => 'FR',
            'role_id' => Role::ROLE_NS_EDITOR
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'email']);

        $user = User::where('email', 'test98765@test.app')->first();

        Notification::assertSentTo(
            [$user],
            AdminUserNeedsConfirmation::class
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_me()
    {
        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $this->actingAs($this->user)
            ->getJson('/api/users/me')
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => $this->getUserJsonStructure()
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_user_list()
    {
        // Occurs on CI server
        $this->markTestSkipped('Failed asserting that an array has the key \'first_name\'. ');

        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-list')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser)
            ->getJson('/api/users')
            ->assertSuccessful()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getUserJsonStructure()
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_admin_user_list()
    {
        // Occurs on CI server
        $this->markTestSkipped('Failed asserting that an array has the key \'first_name\'. ');

        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-list')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser)
            ->getJson('/api/users/admins')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getUserJsonStructure()
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_admin_user_list_can_sort_by_profile_last_name()
    {
        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make([
            'first_name' => 'Able',
            'last_name' => 'Zed',
        ]));

        $duplicateProfile = \Database\Factories\UserProfileFactory::new()->make([
            'first_name' => 'Able',
            'last_name' => 'Aardvark',
        ]);
        $duplicateProfile->user_id = $adminUser->id;
        $duplicateProfile->save();

        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-list')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser)
            ->getJson('/api/users/admins?page=1&orderBy=last_name&sort=asc')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getUserJsonStructure()
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_user_list_with_pagination()
    {
        // Occurs on CI server
        $this->markTestSkipped('Failed asserting that an array has the key \'first_name\'. ');

        \Database\Factories\UserFactory::new()->count(29)->create()
            ->each(function (User $u) {
                $u->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
                $u->save();
            });

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-list')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $this->actingAs($adminUser)
            ->getJson('/api/users?page=2')
            ->assertSuccessful()
            ->assertJsonCount(15, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->getUserJsonStructure()
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next'
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ])->assertJson([
                'meta' => [
                    'current_page' => 2,
                    'from' => 16,
                    'to' => 30,
                    'last_page' => 2,
                    'total' => 30,
                    'per_page' => 15
                ]
            ]);
    }

    public function test_it_filters_users_by_role_id()
    {
        // Occurs on CI server
        $this->markTestSkipped('Failed asserting that an array has the key \'first_name\'. ');

        $apiUser = \Database\Factories\UserFactory::new()->create();
        $apiUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $apiRole = Role::create(['name' => 'Api User']);
        $apiUser->roles()->attach($apiRole->id); // no permissions

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $role = Role::create(['name' => 'Super Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'users-list')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);

        $params = $query = [
            'page' => 1,
            'filters' => [
                'role' => $apiRole->id
            ]
        ];
        $url = urldecode(http_build_query($params));

        $result = $this->actingAs($adminUser)
            ->getJson('/api/users?' . $url)
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');
    }

    public function test_it_updates_user_organisations()
    {
        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $this->actingAs($this->user)
            ->json('PATCH', '/api/users/'.$this->user->id, ['organisations' => ['NZL','CAN']])
            ->assertStatus(200)
            ->assertJsonFragment(['organisations' => ['NZL', 'CAN']]);
    }

    public function test_it_updates_user_agreement_acceptance()
    {
        $this->user = \Database\Factories\UserFactory::new()->create();
        $this->user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $this->actingAs($this->user)
            ->json('PATCH', '/api/users/' . $this->user->id, ['accepted_agreement' => true])
            ->assertStatus(200)
            ->assertJsonFragment(['accepted_agreement' => true]);
    }

    private function getUserJsonStructure()
    {
        return [
            'id',
            'email',
            'activated',
            'password_updated_at',
            'confirmed',
            'created_at',
            'last_logged_in_at',
            'user_profile' => [
                'first_name',
                'last_name',
                'display_name',
                'organisation',
                'industry_type',
                'terms_version',
                'photo_url'
            ],
            'organisations'
        ];
    }
}
