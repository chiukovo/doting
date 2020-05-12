<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddAdmin extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account', 100)->unique()->comment('帳號');
            $table->string('password', 100)->comment('密碼');
            $table->enum('status', [0, 1])->default(1)->comment('0: 關閉, 1: 開啟');
            $table->dateTime('last_login')->nullable()->comment('最後登入時間');
            $table->string('last_ip')->nullable()->comment('最後登入IP');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('admin')->insert([
            'account'  => 'admin',
            'password' => Hash::make('doting8877'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
