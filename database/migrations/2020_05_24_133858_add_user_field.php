<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('web_user', function ($table) {
            $table->string('passport', 150)->nullable()->after('picture_url');
            $table->string('island_name', 150)->nullable()->after('passport');
            $table->integer('fruit')->default(0)->comment('1:桃子, 2:蘋果, 3:梨子, 4:櫻桃, 5:橘子')->after('island_name');
            $table->integer('position')->default(0)->comment('1:南, 2:北')->after('fruit');
            $table->string('info', 150)->nullable()->after('position');
            $table->string('flower', 150)->nullable()->after('info');
            $table->string('nick_name', 150)->nullable()->after('flower');
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
            $table->dropColumn('passport');
            $table->dropColumn('island_name');
            $table->dropColumn('position');
            $table->dropColumn('fruit');
        });
    }
}
