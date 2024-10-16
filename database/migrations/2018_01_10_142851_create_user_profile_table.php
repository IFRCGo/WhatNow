<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfileTable extends Migration
{
    
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('first_name', 191)->index();
            $table->string('last_name', 191)->index();
            $table->string('country_code',2)->default('GB');
            $table->string('organisation')->nullable();
            $table->string('industry_type')->nullable();
            $table->string('terms_version', 10)->nullable();
            $table->boolean('accepted_agreement')->default(false);
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        if(env('DB_CONNECTION') === 'mysql'){
            DB::statement('ALTER TABLE user_profiles ADD FULLTEXT first_name_search(first_name)');
            DB::statement('ALTER TABLE user_profiles ADD FULLTEXT last_name_search(last_name)');
        }
    }

    
    public function down()
    {
        if(env('DB_CONNECTION') === 'mysql') {
            Schema::table('user_profiles', function (Blueprint $table) {
                $keyExists = DB::select(
                    DB::raw(
                        'SHOW KEYS
                    FROM user_profiles
                    WHERE Key_name=\'first_name_search\''
                    )
                );

                if ($keyExists) {
                    $table->dropIndex('first_name_search');
                }

                $keyExists = DB::select(
                    DB::raw(
                        'SHOW KEYS
                    FROM user_profiles
                    WHERE Key_name=\'last_name_search\''
                    )
                );

                if ($keyExists) {
                    $table->dropIndex('last_name_search');
                }
            });
        }

        Schema::dropIfExists('user_profiles');
    }
}
