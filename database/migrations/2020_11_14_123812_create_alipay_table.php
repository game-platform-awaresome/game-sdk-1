<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedSmallInteger('app_id')->comment('应用id');
            $table->string('open_id')->comment('支付宝的app_id');
            $table->text('ali_public_key')->comment('支付宝公钥，用于验签支付宝回调数据');
            $table->text('private_key')->comment('应用私钥，用于下单生成签名');
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
        Schema::dropIfExists('alipay');
    }
}
