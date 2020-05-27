<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnimalField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('avatar_url', 200)->nullable()->commit('頭像路徑');
            $table->enum('status', [0, 1])->default(1)->comment('0: 停用, 1: 開啟');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('animal', function ($table) {
            $table->dropColumn('avatar_url');
            $table->dropColumn('status');
        });
    }
}
