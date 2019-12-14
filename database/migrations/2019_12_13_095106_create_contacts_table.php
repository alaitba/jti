<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contact_code')->unique();
            $table->string('contact_type');
            $table->string('mobile_phone');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('contact_uid')->unique();
            $table->string('iin_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contact_code', 'contact_uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
