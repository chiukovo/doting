<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixAnimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animal', function ($table) {
            $table->string('info', 100)->nullable()->commit('info');
            $table->dropColumn('img_source');
            $table->dropColumn('img_path');
            $table->dropColumn('img_upload_success');
            $table->dropColumn('beautify_img');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
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
            $table->string('img_source')->nullable()->comment('原路徑圖片');
            $table->string('img_path')->nullable()->comment('本站圖片');
            $table->integer('img_upload_success')->default(0)->comment('上傳成功');
            $table->string('personality', 100)->nullable()->comment('個性');
            $table->integer('beautify_img')->default(0)->after('img_upload_success')->commit('美化');
            $table->timestamps();
        });
    }
}
