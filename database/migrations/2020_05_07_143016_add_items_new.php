<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemsNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_new', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('名稱');
            $table->string('jp_name', 150)->nullable()->comment('jp名稱');
            $table->string('en_name', 150)->nullable()->comment('en名稱');
            $table->integer('is_diy')->default(0)->comment('是否能diy');
            $table->integer('type')->default(0)->comment('家具0, 衣服1');
            $table->string('img_name', 100)->nullable()->comment('img_name');
            $table->string('admin_img_name', 100)->nullable()->comment('admin_img_name');
            $table->string('color', 100)->nullable()->comment('color');
            $table->string('buy', 100)->nullable()->comment('buy');
            $table->string('sell', 100)->nullable()->comment('sell');
            $table->string('size', 100)->nullable()->comment('size');
            $table->string('tag', 100)->nullable()->comment('tag');
            $table->string('info', 200)->nullable()->commit('info');
            $table->string('category', 100)->nullable()->comment('category');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items_new');
    }
}
