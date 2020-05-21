<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizResultsExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_results_exports', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->string('phone');
            $table->string('quiz');
            $table->string('date');
            $table->string('bonus');
            $table->longText('question');
            $table->longText('answer')->nullable();
            $table->string('correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_results_exports');
    }
}
