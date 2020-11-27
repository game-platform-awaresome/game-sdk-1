<?php


namespace App\Contracts;


interface PayInterface
{
    // 构造函数
    public function __construct(int $app_id);

    // 统一下单接口
    public function unifiedOrder(array $data);

    // 回调处理
    public function notify();

    // 查找订单
    public function findOutTradeOrder(string $orderId);
}
