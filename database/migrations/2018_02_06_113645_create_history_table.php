<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration
{
    
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('country_code', 3)->nullable();
            $table->string('language_code', 2)->nullable();
            $table->string('action');
            $table->string('content');
            $table->integer('entity_id')->nullable();
            $table->timestamp('created_at');
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('history');
    }
}
