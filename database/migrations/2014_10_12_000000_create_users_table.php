<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->dateTime('password_updated_at')->nullable();
            $table->string('confirmation_code');
            $table->boolean('confirmed')->default(0);
            $table->boolean('activated')->default(1);
            $table->timestamp('last_logged_in_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
