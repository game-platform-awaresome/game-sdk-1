<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id',20)->index()->comment('账户id');
            $table->char('uuid', 64)->index()->nullable()->comment('用户识别码：客户端上传；如果是正常用户则不存在uuid');
            $table->string('phone', 20)->index()->comment('电话号码');
            $table->string('name', 64)->comment('昵称');
            $table->string('password')->comment('密码');
            $table->unsignedTinyInteger('user_type')->comment('用户类型 1：游客 0：正常用户');
            $table->unsignedTinyInteger('os')->comment('注册系统 1: android 2:ios');
            $table->string('device')->comment('注册设备');
            $table->unsignedSmallInteger('app_id')->comment('注册游戏');
            $table->char('ip', 24)->comment('注册ip');
            $table->string('token', 512)->nullable()->comment('用户登陆token');
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
        Schema::dropIfExists('accounts');
    }
}
