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
            $table->increments('id');
            $table->string('open_id',128)->index()->comment('唯一的账户id');
            $table->string('phone', 20)->index()->comment('电话号码');
            $table->unsignedTinyInteger('os')->comment('系统 1: android 2:ios');
            $table->unsignedTinyInteger('user_type')->comment('用户类型 1：游客 0：正常用户');
            $table->string('device')->comment('设备');
            $table->char('ip', 24)->comment('登录ip');
            $table->timestamps();
        });
    }
}