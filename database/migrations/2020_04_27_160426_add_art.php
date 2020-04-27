<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('art', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('名稱');
            $table->string('cn_name', 100)->comment('cn名稱');
            $table->string('img1', 100)->nullable()->comment('img1');
            $table->string('img2', 100)->nullable()->comment('img2');
            $table->string('img3', 100)->nullable()->comment('img3');
            $table->string('info', 100)->nullable()->comment('info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('art');
    }
}
