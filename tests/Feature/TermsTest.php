<?php

namespace Tests\Feature;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use App\Models\Terms;
use App\Notifications\Terms\TermsUpdatedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TermsTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from terms');
    }

    public function test_get_terms()
    {
        Terms::create([
            'version' => 1.1,
            'content' => 'Test',
            'created_at' => new Carbon(),
            'user_id' => 1
        ]);

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $this->actingAs($user);
        $this->getJson('/api/terms/latest')->assertSuccessful()->assertJsonStructure(['data' => ['version', 'content', 'createdAt']]);
    }

    public function test_list_terms()
    {
        Terms::create([
            'version' => 1.1,
            'content' => 'Test',
            'created_at' => new Carbon(),
            'user_id' => 1
        ]);

        Terms::create([
            'version' => 1.2,
            'content' => 'Test',
            'created_at' => new Carbon(),
            'user_id' => 1
        ]);

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $this->actingAs($user);
        $this->getJson('/api/terms')->assertSuccessful()->assertJsonStructure(['data' => ['*' => ['version', 'content', 'createdAt']]]);
    }

    public function test_post_terms()
    {
        Notification::fake();

        $basicRole = Role::create(['name' => 'Basic User']);

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->roles()->attach($basicRole->id);

        $user2 = factory(User::class)->create();
        $user2->userProfile()->save(factory(UserProfile::class)->states(['notifications_off'])->make());
        $user2->roles()->attach($basicRole->id);

        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'terms-update')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);
        $this->actingAs($adminUser);
        $this->postJson('/api/terms', [
            'version' => '1',
            'content' => 'Test',
        ])
            ->assertStatus(201)
            ->assertJsonStructure(['data' => ['version', 'content', 'createdAt']]);

        Notification::assertSentTo(
            [$user],
            TermsUpdatedNotification::class
        );

        Notification::assertNotSentTo(
            [$adminUser, $user2],
            TermsUpdatedNotification::class
        );
    }

    public function test_cannot_duplicate_version()
    {
        Terms::create([
            'version' => 1.1,
            'content' => 'Test',
            'created_at' => new Carbon(),
            'user_id' => 1
        ]);

        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $role = Role::create(['name' => 'Basic Admin']);
        $role->permissions()
            ->sync([
                Permission::where('name', '=', 'terms-update')->first()->id,
            ]);
        $adminUser->roles()->attach($role->id);
        $this->actingAs($adminUser);
        $this->postJson('/api/terms', [
            'version' => '1.1',
            'content' => 'Test',
            'user_id' => 1
        ])
            ->assertStatus(422);
    }
}
