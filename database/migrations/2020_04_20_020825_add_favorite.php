<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFavorite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 200)->nullable()->comment('line id');
            $table->string('display_name', 200)->nullable()->comment('display_name');
            $table->string('table_name', 50)->nullable()->comment('table_name');
            $table->integer('table_id')->default(0)->comment('id');
            $table->index(['user_id', 'table_name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorite');
    }
}
