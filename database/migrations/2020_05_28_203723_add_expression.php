<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpression extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expression', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('en_name', 150);
            $table->string('jp_name', 150);
            $table->string('img_name', 150);
            $table->string('from', 150);
            $table->string('source', 200)->default('[]');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expression');
    }
}
