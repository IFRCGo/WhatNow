<?php

namespace Tests\Browser;

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserProfile;
use Tests\Browser\Pages\Login;
use Tests\Browser\Pages\UserList;
use Tests\DuskTestCase;

class UserListTest extends DuskTestCase
{
    public function setUp(): void
    {
        parent::setup();

        static::closeAll();
    }

    /** @test */
    public function user_list_page_renders()
    {
        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make(['accepted_agreement' => true]));
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->clickLink('Manage Content Users')
                ->assertPageIs(UserList::class)
                ->waitFor('.table')
                ->assertVisible('.table');
        });
    }

    /** @test */
    public function user_can_edit()
    {
        $user = factory(User::class)->create();
        $user->userProfile()->save(factory(UserProfile::class)->make(['accepted_agreement' => true]));
        $user->roles()->attach(Role::ROLE_GDPC_ADMIN);

        $this->browse(function ($browser) use ($user) {
            $browser->visit(new Login)
                ->submit($user->email, 'secret')
                ->clickLink('Manage Content Users')
                ->assertPageIs(UserList::class);
        });
    }
}
