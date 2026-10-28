<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Access\UserTableSeeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(TermsSeeder::class);
        $this->call(EventTypesSeeder::class);
    }
}
