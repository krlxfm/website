<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MoveBoostDataToOwnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boosts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('show_id');
            $table->enum('type', ['S', 'A1', 'zone'])->default('S');
            $table->timestamps();
        });

        Schema::table('show_user', function (Blueprint $table) {
            $table->dropColumn('boost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boosts');

        Schema::table('show_user', function (Blueprint $table) {
            $table->string('boost')->nullable();
        });
    }
}
