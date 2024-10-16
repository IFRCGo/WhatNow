<?php

namespace Tests\Browser;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Tests\Feature\ReadsFrontendTranslations;

class SidebarTest extends DuskTestCase
{
    use ReadsFrontendTranslations;

    public function setUp(): void
    {
        parent::setup();

        static::closeAll();
    }

    /** @test */
    public function login_check_sidebar_gdpc_admin()
    {
        $this->markTestSkipped('Needs to expand sidebar groups to check they are there');

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->resize(1025, 768); // Width and Height
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->waitForLocation('/home')
                ->assertPageIs(Home::class)
                ->assertVisible('.sidebar')
                ->assertSee($this->trans('sidebar.manage'))
                ->assertSee($this->trans('sidebar.content'))
                ->assertSee($this->trans('sidebar.api'));
        });
    }

    /** @test */
    public function login_check_sidebar_NS_admin()
    {
        $this->markTestSkipped('Needs to expand sidebar groups to check they are there');

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->roles()->attach(Role::ROLE_NS_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->waitForLocation('/home')
                ->assertPageIs(Home::class)
                ->assertVisible('.sidebar')
                ->assertSee($this->trans('sidebar.manage'))
                ->assertSee($this->trans('sidebar.content'))
                ->assertSee($this->trans('sidebar.api'));
        });
    }

    /** @test */
    public function login_check_sidebar_api_user()
    {
        $this->markTestSkipped('Needs to expand sidebar groups to check they are there');

        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make());
        $user->roles()->attach(Role::ROLE_API_USER);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->waitForLocation('/home')
                ->assertPageIs(Home::class)
                ->assertMissing('.sidebar');
        });
    }
}
