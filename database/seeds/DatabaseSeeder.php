<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(TermsSeeder::class);
        $this->call(EventTypesSeeder::class);
    }
}
