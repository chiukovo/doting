<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable()->comment('名稱');
            $table->string('cn_name', 150)->nullable()->comment('cn名稱');
            $table->string('type', 100)->nullable()->comment('類型');
            $table->string('img_name', 100)->nullable()->comment('img_name');
            $table->string('source_sell', 100)->nullable()->comment('source sell');
            $table->string('sell', 100)->nullable()->comment('sell');
            $table->string('sample_sell', 100)->nullable()->comment('sample_sell');
            $table->string('buy_type', 100)->nullable()->comment('buy_type');
            $table->string('detail_type', 100)->nullable()->comment('detail_type');
            $table->string('size', 100)->nullable()->comment('size');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
