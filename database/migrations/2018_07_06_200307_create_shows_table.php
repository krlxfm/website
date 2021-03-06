<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            // Technical administration
            $table->string('id', 40)->primary();
            $table->string('priority')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Categorization
            $table->string('term_id', 30);
            $table->integer('track_id')->unsigned();
            $table->string('source')->default('web');

            // Status
            $table->boolean('submitted')->default(false);

            // Content
            $table->string('title');
            $table->text('description');
            $table->text('tags');
            $table->text('image');
            $table->text('content');
            $table->boolean('fallback')->default(true);

            // Scheduling
            $table->text('special_times');
            $table->tinyInteger('preferred_length')->unsigned()->default(60);
            $table->text('classes');
            $table->text('conflicts');
            $table->text('preferences');
            $table->text('notes')->nullable();
            $table->text('scheduling');

            // Time assignment for recurring shows
            $table->enum('day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();

            // Time assignment for single shows is handled via the track,
            // but the date needs to be set.
            $table->date('date')->nullable();

            // Schedule publication: additional information is needed to keep
            // track of what data has been sent out to Google Calendar, versus
            // what is being worked on right now.
            $table->enum('published_day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->nullable();
            $table->date('published_date')->nullable();
            $table->string('published_start')->nullable();
            $table->string('published_end')->nullable();
            $table->string('gc_show_id')->nullable();

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
        Schema::dropIfExists('shows');
    }
}
