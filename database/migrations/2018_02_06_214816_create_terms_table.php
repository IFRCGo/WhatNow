<?php

    use App\Models\Terms;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;

    class CreateTermsTable extends Migration
    {
        
        public function up()
        {
            Schema::create('terms', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->string('version')->unique();
                $table->text('content');
                $table->timestamps();
            });

            Terms::create([
                'user_id' => 2,
                'version' => 0.1,
                'content' => 'Terms and Conditions'
            ]);
        }

        
        public function down()
        {
            Schema::dropIfExists('terms');
        }
    }
