<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('imageable_id');
            $table->string('imageable_type');
            $table->boolean('main_image')->default(0);
            $table->string('client_file_name')->nullable();
            $table->string('original_file_name')->nullable();
            $table->json('conversions')->nullable();
            $table->unsignedInteger('order')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime', 127)->nullable();
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
        Schema::dropIfExists('media');
    }
}
