<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_code');
            $table->integer('bonus_portfolio');
            $table->integer('bonus_brand');
            $table->integer('plan_portfolio');
            $table->integer('plan_brand');
            $table->integer('fact_portfolio');
            $table->integer('fact_brand');
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
        Schema::dropIfExists('sales_plans');
    }
}
