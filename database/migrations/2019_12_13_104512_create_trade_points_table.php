<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_code');
            $table->string('account_name');
            $table->string('street_address');
            $table->string('city');
            $table->string('employee_code');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_code', 'account_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_points');
    }
}
