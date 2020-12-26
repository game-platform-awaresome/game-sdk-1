<?php


namespace App\Traits;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait OrderRepositoryTrait
{
    // 做补充
    public static function migrate(string $tableName)
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id', 64)->unique()->index()->comment('平台订单id');
            $table->string('open_id', 64)->comment('账号id');
            $table->string('device', 32)->comment('手机设备号');
            $table->string('server_id', 32)->comment('服务器id');
            $table->string('role_id', 64)->comment('角色id');
            $table->string('role_name', 32)->comment('角色名称');
            $table->string('item_id', 32)->comment('计费点id');
            $table->string('item_name', 32)->comment('商品名称');
            $table->decimal('amount')->comment('发起购买金额');
            $table->string('extra_data', 1024)->nullable()->comment('额外参数');
            $table->string('ip', 32)->comment('ip地址');
            $table->unsignedTinyInteger('os')->comment('系统（1: android 2:ios）');
            $table->unsignedTinyInteger('order_type')->comment('订单类型(0：测试订单 1：正常订单)');
            $table->string('sdk_version', 32)->comment('客户端版本');
            $table->string('cp_order_no', 64)->comment('游戏方支付流水号');
            $table->string('channel_id', 32)->nullable()->comment('渠道id');
            $table->string('sub_chan_merchant', 32)->nullable()->comment('子渠道id');
            $table->decimal('callback_amount')->nullable()->comment('回调购买金额');
            $table->unsignedTinyInteger('pay_channel')->nullable()->comment('充值渠道（1：微信 2：支付宝）');
            $table->string('out_order_no', 64)->nullable()->comment('充值流水号（微信或支付宝流水号）');
            $table->timestamp('pay_time', 32)->nullable()->comment('支付回调时间');
            $table->unsignedTinyInteger('status')->comment('订单状态（查看枚举类OrderStatus）');
            $table->timestamps();
        });
    }
}