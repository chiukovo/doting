<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnimalTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('target', 100)->nullable()->after('info')->commit('目標');
            $table->string('motto', 100)->nullable()->commit('座右銘');
            $table->string('kk', 100)->nullable()->commit('kk');
            $table->string('amiibo', 100)->nullable()->commit('amiibo');
            $table->string('level', 100)->nullable()->commit('level');
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
            $table->dropColumn('target');
            $table->dropColumn('motto');
            $table->dropColumn('kk');
            $table->dropColumn('amiibo');
            $table->dropColumn('level');
        });
    }
}
