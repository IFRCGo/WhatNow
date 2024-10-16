<?php

use Illuminate\Database\Seeder;

class TermsSeeder extends Seeder
{
    
    public function run()
    {
        factory(\App\Models\Terms::class, 30)->create();
    }
}
