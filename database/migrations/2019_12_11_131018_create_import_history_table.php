<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('admin_id')->default(0);
            $table->string('type');
            $table->integer('added')->unsigned()->default(0);
            $table->integer('updated')->unsigned()->default(0);
            $table->integer('deleted')->unsigned()->default(0);
            $table->boolean('failed')->default(1);
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('import_history');
    }
}
