<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('active')->default(false);

            $table->string('name');
            $table->text('description');

            // Bells and whistles
            $table->boolean('boostable')->default(true);
            $table->boolean('clonable')->default(true);
            $table->boolean('allows_images')->default(true);
            $table->boolean('can_fall_back')->default(true);
            $table->boolean('taggable')->default(true);
            $table->boolean('awards_xp')->default(true);

            // Priority
            $table->string('prefix')->nullable();
            $table->char('zone', 1)->nullable();
            $table->tinyInteger('group')->unsinged()->nullable();
            $table->smallInteger('order')->unsigned()->default(1000);

            // Group size and participants
            $table->boolean('allows_direct_add')->default(false);
            $table->boolean('joinable')->default(true);
            $table->tinyInteger('max_participants')->unsigned()->nullable();

            // Content
            $table->string('title_label')->nullable();
            $table->string('description_label')->nullable();
            $table->smallInteger('description_min_length')->unsigned()->default(0);
            $table->text('content');

            // Scheduling
            $table->boolean('weekly')->default(true);
            $table->enum('start_day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->text('scheduling');

            // Additional questions
            $table->text('etc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
