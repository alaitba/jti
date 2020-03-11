<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_auths', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('partner_id');
            $table->string('account_code');
            $table->dateTime('login');
            $table->dateTime('last_seen');
            $table->string('os');
            $table->timestamps();

            $table->unique(['partner_id', 'account_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_auths');
    }
}
