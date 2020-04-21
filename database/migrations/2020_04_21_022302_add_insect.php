<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsect extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insect', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable()->comment('魚');
            $table->string('position', 50)->nullable()->comment('位置');
            $table->string('time', 100)->nullable()->comment('天');
            $table->integer('sell')->default(0)->comment('賣價');
            $table->string('m1', 50)->nullable()->comment('1');
            $table->string('m2', 50)->nullable()->comment('2');
            $table->string('m3', 50)->nullable()->comment('3');
            $table->string('m4', 50)->nullable()->comment('4');
            $table->string('m5', 50)->nullable()->comment('5');
            $table->string('m6', 50)->nullable()->comment('6');
            $table->string('m7', 50)->nullable()->comment('7');
            $table->string('m8', 50)->nullable()->comment('8');
            $table->string('m9', 50)->nullable()->comment('9');
            $table->string('m10', 50)->nullable()->comment('10');
            $table->string('m11', 50)->nullable()->comment('11');
            $table->string('m12', 50)->nullable()->comment('12');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insect');
    }
}
