<?php

namespace Tests\Browser;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Tests\Feature\ReadsFrontendTranslations;

class LoginTest extends DuskTestCase
{
    use ReadsFrontendTranslations;

    public function setUp(): void
    {
        parent::setup();

        static::closeAll();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_with_valid_credentials()
    {
        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->waitForLocation('/home')
                ->assertPageIs(Home::class);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function login_with_invalid_credentials()
    {
        $this->browse(function ($browser) {
            $browser->visit(new Login)
                ->submit('test@test.app', 'secret')
                ->waitUntilMissing('.btn-loading')
                ->assertSee('These credentials do not match our records.');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function log_out_the_user()
    {
        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->pause(5000)
                ->on(new Home)
                ->clickLogout()
                ->pause(5000)
                ->on(new Login)
                ->assertPageIs(Login::class);
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function forgot_password_flow()
    {
        $user = \Database\Factories\UserFactory::new()->create();
        $user->userProfile()->save(\Database\Factories\UserProfileFactory::new()->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
            ->clickLink($this->trans('forgot_password'))
            ->waitForLocation('/password/reset')
            ->waitFor('input[name=email]')
            ->type('input[name=email]', $user->email)
            ->press('Send Password Reset Link')
            ->waitFor('div.alert')
            ->assertSee(trans('passwords.sent'));
        });
    }
}
