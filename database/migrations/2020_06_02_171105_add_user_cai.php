<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserCai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cai', function (Blueprint $table) {
            $table->increments('id');
            $table->string('line_id', 150);
            $table->string('start', 150);
            $table->string('end', 150);
            $table->string('cai', 200)->default('[]');
            $table->string('result', 150);
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
        Schema::dropIfExists('user_cai');
    }
}
