<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->nullable()->comment('名稱');
            $table->string('type', 100)->nullable()->comment('類型');
            $table->string('get', 200)->nullable()->comment('獲取方式');
            $table->string('diy', 200)->nullable()->comment('diy');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diy');
    }
}
