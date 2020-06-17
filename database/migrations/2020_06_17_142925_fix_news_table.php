<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('contents');
            $table->date('to_date')->nullable()->after('from_date');
            $table->boolean('public')->after('to_date');
            $table->string('user_list_file')->nullable()->after('public');
            $table->boolean('user_list_imported')->default(false)->after('user_list_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('from_date');
            $table->dropColumn('to_date');
            $table->dropColumn('public');
            $table->dropColumn('user_list_file');
            $table->dropColumn('user_list_imported');
        });
    }
}
