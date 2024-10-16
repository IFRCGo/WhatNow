<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTypesTable extends Migration
{
    
    public function up()
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('icon');
            $table->timestamps();
        });

        \App\Models\EventType::create([
            'name' =>'Other',
            'icon' => 'general@3x.png',
            'code' => 'other'
        ]);
    }

    
    public function down()
    {
        Schema::dropIfExists('event_types');
    }
}
