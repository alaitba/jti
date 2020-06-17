<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsPartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('news_id');
            $table->bigInteger('partner_id');
            $table->timestamps();
            $table->unique(['news_id', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_partners');
    }
}
