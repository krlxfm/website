<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBoardAppModelsAndFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->boolean('active')->default(true);
            $table->boolean('on_call')->default(false);
            $table->boolean('restricted')->default(false);
            $table->string('color')->default('#aa0000');
            $table->string('title');
            $table->string('abbr');
            $table->text('description')->nullable();
            $table->text('app_questions')->nullable();
            $table->text('interview_questions')->nullable();
        });

        Schema::create('board_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->integer('year')->unsigned();
            $table->boolean('submitted')->default(false);
            $table->enum('ocs', ['none', 'abroad_sp', 'abroad_fa', 'abroad_wi'])->default('none');

            // Interview scheduling preferences and assigned time
            $table->text('interview_schedule')->nullable();
            $table->dateTime('interview')->nullable();

            // Logistical stuff - candidates who are abroad are interviewed
            // over video conference.
            $table->boolean('remote')->default(false);
            $table->string('remote_platform')->nullable();
            $table->string('remote_contact')->nullable();

            // Answers to the common questions.
            $table->text('common')->nullable();
        });

        Schema::create('position_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->tinyInteger('order')->unsigned()->default(0);
            $table->integer('board_app_id')->unsigned();
            $table->integer('position_id')->unsigned();
            $table->text('responses')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('pronouns')->nullable();
            $table->string('walkup')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('position_apps');
        Schema::drop('positions');
        Schema::drop('board_apps');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pronouns', 'walkup']);
        });
    }
}
