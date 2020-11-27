<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $order_id 平台订单id
 * @property string $open_id 账号id
 * @property string $device 手机设备号
 * @property string $server_id 服务器id
 * @property string $role_id 角色id
 * @property string $role_name 角色名称
 * @property string $item_id 计费点id
 * @property string $item_name 商品名称
 * @property string $amount 发起购买金额
 * @property string|null $extra_data 额外参数
 * @property string $ip ip地址
 * @property int $os 系统（1: android 2:ios）
 * @property int $order_type 订单类型(0：测试订单 1：正常订单)
 * @property string $sdk_version 客户端版本
 * @property string|null $cp_order_no 游戏方支付流水号
 * @property string|null $channel_id 渠道id
 * @property string|null $sub_chan_merchant 子渠道id
 * @property string|null $callback_amount 回调购买金额
 * @property int|null $pay_channel 充值渠道（1：微信 2：支付宝）
 * @property string|null $pay_order_no 充值流水号（微信或支付宝流水号）
 * @property string|null $pay_time 支付回调时间
 * @property int $status 订单状态（1：下单 2：支付取消 3：支付失败 4：发货成功 9：校验失败 -1：发货失败）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @mixin \Eloquent
 * @mixin Builder
 */
class Order extends Model
{
    protected $table = 'orders_1';

    protected $guarded = [];
}
