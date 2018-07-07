<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->text('description')->nullable();
            $table->text('tags');
            $table->text('image');
            $table->text('content');

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
