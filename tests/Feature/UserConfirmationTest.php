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

    #[\PHPUnit\Framework\Attributes\Test]
    public function resend_confirmation_email_for_users()
    {
        Notification::fake();

        /** @var User $user */
        $user = \Database\Factories\UserFactory::new()->unconfirmed()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());


        /** @var User $adminUser */
        $adminUser = \Database\Factories\UserFactory::new()->create();
        $adminUser->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $adminUser->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->actingAs($adminUser);
        $this->getJson('/api/users/'.$user->id.'/resend')
            ->assertSuccessful();

        Notification::assertSentTo(
            [$user],
            UserNeedsConfirmation::class
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function confirm_email_when_password_was_set()
    {
        $user = \Database\Factories\UserFactory::new()->unconfirmed()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $this->getJson('/api/confirm/'.$user->confirmation_code)->assertRedirect(route('login.account_confirmed'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_invalid_confirmation_code()
    {
        $user = \Database\Factories\UserFactory::new()->unconfirmed()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $this->getJson('/api/confirm/blaaah')->assertRedirect(route('login.confirmation_failed'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function confirm_email_when_password_was_not_set()
    {
        $user = \Database\Factories\UserFactory::new()->unconfirmed()->noPassword()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());

        $response = $this->getJson('/api/confirm/'.$user->confirmation_code);
        $response->isRedirect();

        $this->assertGreaterThan(0, strpos($response->headers->get('Location'), 'password/set/'));
    }
}
