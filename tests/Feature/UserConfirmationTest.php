<?php

namespace Tests\Feature;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use App\Notifications\Auth\UserNeedsConfirmation;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserConfirmationTest extends TestCase
{
    /** @var \App\Models\Access\User\User */
    protected $user;

    /** @test */
    public function resend_confirmation_email_for_users()
    {
        Notification::fake();

        /** @var User $user */
        $user = factory(User::class)->states(['unconfirmed'])->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());


        /** @var User $adminUser */
        $adminUser = factory(User::class)->create();
        $adminUser->userProfile()->save(factory(UserProfile::class)->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser);
        $this->getJson('/api/users/'.$user->id.'/resend')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$user],
            UserNeedsConfirmation::class
        );
    }

    /** @test */
    public function confirm_email_when_password_was_set()
    {
        $user = factory(User::class)->states(['unconfirmed'])->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());

        $this->getJson('/api/confirm/'.$user->confirmation_code)->assertRedirect(route('login.account_confirmed'));
    }

    /** @test */
    public function test_invalid_confirmation_code()
    {
        $user = factory(User::class)->states(['unconfirmed'])->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());

        $this->getJson('/api/confirm/blaaah')->assertRedirect(route('login.confirmation_failed'));
    }

    /** @test */
    public function confirm_email_when_password_was_not_set()
    {
        $user = factory(User::class)->states(['unconfirmed', 'no-password'])->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());

        $response = $this->getJson('/api/confirm/'.$user->confirmation_code);
        $response->isRedirect();

        $this->assertGreaterThan(0, strpos($response->headers->get('Location'), 'password/set/'));
    }
}
