<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('sex', 100)->nullable()->after('jp_name')->commit('sex');
            $table->string('bd_m', 100)->nullable()->after('bd')->commit('月份');
            $table->string('bd_d', 100)->nullable()->after('bd')->commit('日');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('animal', function ($table) {
            $table->dropColumn('sex');
            $table->dropColumn('bd_m');
            $table->dropColumn('bd_d');
        });
    }
}
