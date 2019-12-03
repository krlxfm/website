<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->timestamps();
            $table->dateTime('on_air');
            $table->dateTime('off_air');
            $table->boolean('boosted')->default(false);
            $table->enum('status', ['pending', 'new', 'active', 'closed', 'scheduled'])->default('new');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
