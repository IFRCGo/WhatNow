<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiUsedInFieldToUserProfilesTable extends Migration
{
    
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('api_used_in')->nullable()->after('notifications_enabled');
        });
    }

    
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['api_used_in']);
        });
    }
}
