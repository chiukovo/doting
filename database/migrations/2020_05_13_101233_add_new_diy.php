<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewDiy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diy_new', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable()->comment('名稱');
            $table->string('en_name', 150)->nullable()->comment('名稱');
            $table->string('jp_name', 150)->nullable()->comment('名稱');
            $table->string('sell', 100)->nullable()->comment('sell');
            $table->string('type', 200)->default('[]')->comment('類型');
            $table->string('get', 200)->default('[]')->comment('獲取方式');
            $table->string('diy', 200)->nullable()->comment('diy');
            $table->string('note', 200)->nullable()->comment('note');
            $table->string('size', 100)->nullable()->comment('size');
            $table->string('img_name', 100)->nullable()->comment('img_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diy_new');
    }
}
