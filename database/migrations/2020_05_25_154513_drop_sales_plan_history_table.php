<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSalesPlanHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sales_plan_history');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sales_plan_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_code');
            $table->date('year_month');
            $table->integer('bonus_portfolio')->unsigned();
            $table->integer('bonus_brand')->unsigned();
            $table->integer('plan_portfolio')->unsigned();
            $table->integer('plan_brand')->unsigned();
            $table->integer('fact_portfolio')->unsigned();
            $table->integer('fact_brand')->unsigned();
            $table->timestamps();

            $table->index(['account_code', 'year_month'])->unique();
        });
    }
}
