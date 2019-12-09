<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('owner')->default('common');
            $table->string('type', 16);
            $table->unsignedInteger('group_id')->nullable();
            $table->string('client_file_name');
            $table->string('original_file_name');
            $table->unsignedBigInteger('size');
            $table->json('conversions')->nullable();
            $table->json('meta')->nullable();
            $table->string('mime')->nullable();
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
        Schema::dropIfExists('uploads');
    }
}
