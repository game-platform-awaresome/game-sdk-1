<?php


namespace App\Traits;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait AccountLoginRepositoryTrait
{
    // 做补充
    public static function migrate(string $tableName)
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id')->index()->comment('account主键');
            $table->unsignedTinyInteger('user_type')->comment('登录用户类型 1：游客 0：正常用户');
            $table->unsignedTinyInteger('os')->comment('登录系统 1: android 2:ios');
            $table->unsignedSmallInteger('app_id')->default(3)->comment('登录应用');
            $table->string('device')->comment('登录设备');
            $table->char('ip', 24)->comment('登录ip');
            $table->timestamps();
        });
    }
}