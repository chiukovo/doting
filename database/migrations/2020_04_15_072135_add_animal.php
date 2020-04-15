<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animal', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable()->comment('姓名');
            $table->string('img_source')->nullable()->comment('原路徑圖片');
            $table->string('img_path')->nullable()->comment('本站圖片');
            $table->integer('img_upload_success')->default(0)->comment('上傳成功');
            $table->string('personality', 100)->nullable()->comment('個性');
            $table->string('race', 100)->nullable()->comment('種族');
            $table->string('bd', 100)->nullable()->comment('生日');
            $table->string('say', 100)->nullable()->comment('口頭殘');
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
        Schema::dropIfExists('animal');
    }}
