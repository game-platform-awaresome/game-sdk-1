<?php

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     * 默认创个app_id为1的订单表
     *
     * @return void
     */
    public function up()
    {
        OrderRepository::migrate('orders_1');
        OrderRepository::migrate('orders_2');
        OrderRepository::migrate('orders_3');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_1');
        Schema::dropIfExists('orders_2');
        Schema::dropIfExists('orders_3');
    }
}
