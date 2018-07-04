<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // Name and class year
            $table->string('name');
            $table->string('first_name');
            $table->string('nickname')->nullable();
            $table->string('title');
            $table->integer('year')->unsigned()->default(0);

            // Contact information
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('onecard')->nullable();

            // Profile
            $table->string('major')->nullable();
            $table->string('hometown')->nullable();
            $table->text('photo');
            $table->text('bio')->nullable();
            $table->text('favorite_music')->nullable();
            $table->text('favorite_shows')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
