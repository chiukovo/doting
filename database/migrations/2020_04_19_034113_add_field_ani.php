<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldAni extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('en_name', 100)->nullable()->after('name')->commit('en');
            $table->string('jp_name', 100)->nullable()->after('en_name')->commit('jp');
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
            $table->dropColumn('en_name');
            $table->dropColumn('jp_name');
        });
    }
}
