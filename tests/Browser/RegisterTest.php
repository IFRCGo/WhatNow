<?php

namespace Tests\Browser;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Tests\Browser\Pages\Register;
use Tests\DuskTestCase;
use Tests\Feature\ReadsFrontendTranslations;

class RegisterTest extends DuskTestCase
{
    use ReadsFrontendTranslations;

    public function setUp(): void
    {
        parent::setup();

        static::closeAll();
    }

    /** @test */
    public function register_with_valid_data()
    {
        $this->browse(function ($browser) {
            $browser->visit(new Register)
                ->submit([
                    'name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'test@test.app',
                    'organisation' => 'test',
                    'password' => 'secretisatleast10chars',
                    'password_confirmation' => 'secretisatleast10chars',
                    'api_used_in' => 'This is how i intend to use the api'
                ], [
                    'country_code' => 'FR',
                    'industry_type' => 'Media',
                ])
                ->waitFor('.swal2-modal')
                ->assertSee($this->trans('register_success'));
        });
    }

    /** @test */
    public function can_not_register_with_the_same_twice()
    {
        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Register)
                ->submit([
                    'name' => 'Test',
                    'last_name' => 'User',
                    'email' => $user->email,
                    'organisation' => 'test',
                    'password' => 'secretisatleast10chars',
                    'password_confirmation' => 'secretisatleast10chars',
                ], [
                    'country_code' => 'FR',
                    'industry_type' => 'Media',
                ])
                ->assertSee('The email has already been taken.');
        });
    }
}
