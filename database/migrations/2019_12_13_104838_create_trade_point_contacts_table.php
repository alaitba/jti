<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradePointContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_point_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_code');
            $table->string('contact_code');
            $table->string('contact_uid');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_code', 'contact_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_point_contacts');
    }
}
