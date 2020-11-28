<?php


namespace App\Constants;


class OrderStatus
{
    const INIT = 1;               // 订单初始化
    const PREPAY = 2;             // 预支付
    const CANCEL_PAY = 3;         // 取消订单
    const PAY_FAIL = 4;           // 支付失败
    const DELIVER_SUCCESS = 5;    // 发货成功
    const CHECK_FAIL = 9;         // 金额校验失败
    const DELIVER_FAIL = 0;       // 发货失败

    const MSG = [
        self::INIT => '订单初始化',
        self::PREPAY => '预支付',
        self::CANCEL_PAY => '取消订单',
        self::PAY_FAIL => '支付失败',
        self::DELIVER_SUCCESS => '发货成功',
        self::CHECK_FAIL => '金额校验失败',
        self::DELIVER_FAIL => '发货失败',
    ];
}