<?php

namespace Tests\Feature;

use App\Models\Access\Permission\Permission;
use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use App\Models\Terms;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TermsTest extends TestCase
{

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

        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
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

        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $this->actingAs($user);
        $this->getJson('/api/terms')->assertSuccessful()->assertJsonStructure(['data' => ['*' => ['version', 'content', 'createdAt']]]);
    }

    public function test_post_terms()
    {
        Notification::fake();

        $basicRole = Role::create(['name' => 'Basic User']);

        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach($basicRole->id);

        $user2 = \Database\Factories\UserFactory::new()->create();
        $user2->userProfile()->save(\Database\Factories\UserProfileFactory::new()->notificationsOff()->make());
        $user2->roles()->attach($basicRole->id);

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
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

        Notification::assertNothingSent();
        $this->assertSame([], $this->sentMail);
        $this->assertDatabaseHas('terms', ['version' => '1', 'content' => 'Test']);

    }

    public function test_cannot_duplicate_version()
    {
        Terms::create([
            'version' => 1.1,
            'content' => 'Test',
            'created_at' => new Carbon(),
            'user_id' => 1
        ]);

        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
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
