<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TermsSeeder extends Seeder
{
    
    public function run()
    {
        \Database\Factories\TermsFactory::new()->count(30)->create();
    }
}
