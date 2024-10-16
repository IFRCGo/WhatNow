<?php

use App\Models\Access\Role\Role;
use App\Models\Access\User\User;
use App\Models\Access\User\UserConfirmationToken;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserTableSeeder extends Seeder
{
    
    public function run()
    {
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::table('users')->truncate();
            DB::table('user_roles')->truncate();
        } elseif (DB::connection()->getDriverName() == 'sqlite') {
            DB::statement('DELETE FROM ' . 'users');
            DB::statement('DELETE FROM ' . 'user_profiles');
            DB::statement('DELETE FROM ' . 'user_roles');
        } else {
                        DB::statement('TRUNCATE TABLE ' . 'users' . ' CASCADE');
            DB::statement('TRUNCATE TABLE ' . 'user_profiles' . ' CASCADE');
            DB::statement('TRUNCATE TABLE ' . 'user_roles' . ' CASCADE');
        }


        $userData = [
            [
                'user' => [
                    'email' => 'adam.scholey@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Adam',
                    'last_name' => 'Scholey',
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 1,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'javier@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Javi',
                    'last_name' => 'Santos',
                    'user_id' => 2,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 2,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'tom@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Tom',
                    'last_name' => 'Yeadon',
                    'user_id' => 3,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 3,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'kiri.egington@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Kiri',
                    'last_name' => 'Egington',
                    'user_id' => 4,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 4,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'kevin.borrill@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Kevin',
                    'last_name' => 'Borrill',
                    'user_id' => 5,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 5,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'apiuser@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Steven',
                    'last_name' => 'Rogers',
                    'user_id' => 6,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 6,
                    'role_id' => Role::ROLE_API_USER
                ]
            ],
            [
                'user' => [
                    'email' => 'gdpc@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Tony',
                    'last_name' => 'Stark',
                    'user_id' => 7,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 7,
                    'role_id' => Role::ROLE_GDPC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'nsadmin@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Nick',
                    'last_name' => 'Fury',
                    'user_id' => 8,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 8,
                    'role_id' => Role::ROLE_NS_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'nseditor@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Natasha',
                    'last_name' => 'Romanova',
                    'user_id' => 9,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 9,
                    'role_id' => Role::ROLE_NS_EDITOR
                ]
            ],
            [
                'user' => [
                    'email' => 'jessica.robbins@redcross.org',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Jessica',
                    'last_name' => 'Robbins',
                    'user_id' => 10,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 10,
                    'role_id' => Role::ROLE_GDPC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'bonnie.haskell@gmail.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Bonnie',
                    'last_name' => 'Haskell',
                    'user_id' => 11,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 11,
                    'role_id' => Role::ROLE_GDPC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'Omar.Abou-Samra@redcross.org',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Omar',
                    'last_name' => 'Abou-Samra',
                    'user_id' => 12,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 12,
                    'role_id' => Role::ROLE_GDPC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'Max.Gevinson@redcross.org',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Max',
                    'last_name' => 'Gevinson',
                    'user_id' => 13,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 13,
                    'role_id' => Role::ROLE_GDPC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'olly.fairhall@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Olly',
                    'last_name' => 'Fairhall',
                    'user_id' => 14,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 14,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'robby@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Robby',
                    'last_name' => 'Gee',
                    'user_id' => 15,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 15,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ],
            [
                'user' => [
                    'email' => 'owen@3sidedcube.com',
                    'password' => bcrypt('1234'),
                    'password_updated_at' => Carbon::now(),
                    'confirmation_code' => UserConfirmationToken::generate(),
                    'confirmed' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'user_profile' => [
                    'first_name' => 'Owen',
                    'last_name' => 'Evans',
                    'user_id' => 16,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                'role' => [
                    'user_id' => 16,
                    'role_id' => Role::ROLE_3SC_ADMIN
                ]
            ]
        ];


        $users = array_column($userData, 'user');
        $userProfiles = array_column($userData, 'user_profile');
        $userRoles = array_column($userData, 'role');

        DB::table('users')->insert($users);

        foreach ($userProfiles as $userProfile) {
            $up = new \App\Models\Access\User\UserProfile($userProfile);
            $up->user()->associate($userProfile['user_id']);
            $up->save();
            if ($userProfile['user_id'] == 8 || $userProfile['user_id'] == 9) {
                $up->user->organisations()->save(factory(App\Models\Access\User\UserOrganisation::class)->make());
            }
        }

        DB::table('user_roles')->insert($userRoles);

        factory(User::class, 400)->create()
            ->each(
                function (User $u) {
                    $u->attachRole(Role::find(1));
                    $u->userProfile()->save(factory(App\Models\Access\User\UserProfile::class)->states('notifications_off')->make());
                    $u->save();
                }
            );

        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
