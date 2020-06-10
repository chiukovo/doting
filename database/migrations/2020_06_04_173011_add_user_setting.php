<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_user', function ($table) {
            $table->integer('open_user_data')->default(0)->after('info');
            $table->integer('open_picture')->default(0)->after('open_user_data');
            $table->integer('like')->default(0)->after('open_picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('web_user', function ($table) {
            $table->dropColumn('open_user_data');
            $table->dropColumn('open_picture');
            $table->dropColumn('like');
        });
    }
}
