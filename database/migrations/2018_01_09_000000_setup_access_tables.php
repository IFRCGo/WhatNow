<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class SetupAccessTables extends Migration
{
    
    public function up()
    {
        Schema::create('roles', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->boolean('all')->default(false);
            $table->smallInteger('sort')->default(0)->unsigned();
            $table->timestamps();

            
            $table->unique('name');
        });

        Schema::create('user_roles', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });

        Schema::create('permissions', function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('display_name');
            $table->smallInteger('sort')->default(0)->unsigned();
            $table->timestamps();

            
            $table->unique('name');
        });

        Schema::create('role_permissions', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });
    }

    
    public function down()
    {
        
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles' . '_name_unique');
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropForeign('user_roles' . '_user_id_foreign');
            $table->dropForeign('user_roles' . '_role_id_foreign');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique('permissions' . '_name_unique');
        });

        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropForeign('role_permissions' . '_permission_id_foreign');
            $table->dropForeign('role_permissions' . '_role_id_foreign');
        });

        
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
}
