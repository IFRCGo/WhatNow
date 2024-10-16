<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrganisationsTable extends Migration
{
    
    public function up()
    {
        Schema::create('user_organisations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('organisation_code', 3)->index();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['user_id', 'organisation_code']);
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('user_organisations');
    }
}
