<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFossil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fossil', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('名稱');
            $table->string('cn_name', 100)->comment('cn名稱');
            $table->string('en_name', 100)->comment('en名稱');
            $table->string('jp_name', 100)->comment('jp名稱');
            $table->string('img_name', 100)->comment('img name');
            $table->string('sell', 100)->comment('sell');
            $table->text('info')->comment('info');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fossil');
    }
}
