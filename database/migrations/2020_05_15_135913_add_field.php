<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('constellation', 100)->nullable()->after('bd')->commit('constellation');
        });

        Schema::create('constellation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 100);
            $table->string('name', 100);
            $table->text('result');
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
            $table->dropColumn('constellation');
        });

        Schema::dropIfExists('constellation');
    }
}
