<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->enum('status', ['pending', 'early_access', 'active', 'closed', 'scheduled'])->default('pending');
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
