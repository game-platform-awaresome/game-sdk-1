<?php

namespace App\Services;

use App\Constants\OrderStatus;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderService constructor.
     * @param int $appId
     * @throws RenderException
     */
    public function __construct(int $appId)
    {
        $this->orderRepository = new OrderRepository($appId);
    }

    /**
     * 生成订单号
     *
     * @return string
     * @throws \Exception
     */
    protected function generateOrderId(): string
    {
        $date = date('YmdHis');
        $str = substr(microtime(), 2, 3);
        $rand = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        return $date . $str . $rand;
    }

    /**
     * 创建订单
     *
     * @param array $data
     * @return string
     * @throws RenderException
     * @throws \Exception
     */
    public function createOrder(array $data): string
    {
        $orderId = $this->generateOrderId();
        $this->orderRepository->createOrder($orderId, $data);

        return $orderId;
    }

    /**
     * 取消订单
     *
     * @param string $orderId
     * @throws \Exception
     */
    public function cancelPay(string $orderId): void
    {
        $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::CANCEL_PAY);
    }

    /**
     * @param string $orderId
     * @param int $payChannel
     * @throws RenderException
     */
    public function unifiedOrder(string $orderId, int $payChannel): void
    {
        $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::PREPAY);
        $this->orderRepository->updatePayChannelByOrderId($orderId, $payChannel);
    }

    /**
     * @param $orderId
     * @return bool
     * @throws RenderException
     */
    public function IsStatusInitOrPrepay($orderId): bool
    {
        $orderStatus = $this->orderRepository->getStatusByOrderId($orderId);
        if ($orderStatus != OrderStatus::INIT && $orderStatus != OrderStatus::PREPAY) {
            throw new RenderException(Code::ORDER_STATUS_ERROR, 'Order Status Error');
        }
        return true;
    }

    /**
     * @param $orderId
     * @return array
     * @throws RenderException
     */
    public function getOrderInfo(string $orderId)
    {
        return $this->orderRepository->getOrderByOrderId($orderId)->toArray();
    }

    /**
     * @param string $orderId
     * @return array
     * @throws RenderException
     */
    public function getNotifyOrderInfo(string $orderId)
    {
        $orderInfo = ['order_id', 'item_id', 'item_name', 'role_id', 'server_id', 'amount', 'cp_order_no', 'extra_data'];
        $orderInfo  = $this->orderRepository->getOrderByOrderId($orderId, $orderInfo)->toArray();
        // 排序
        ksort($orderInfo);
        return $orderInfo;
    }

    /**
     * @param string $orderId
     * @return string
     * @throws RenderException
     */
    public function getOrderStatus(string $orderId): string
    {
        return OrderStatus::MSG[$this->orderRepository->getStatusByOrderId($orderId)];
    }

    /**
     * @param $orderId
     * @param $callbackAmount
     * @return bool
     * @throws RenderException
     */
    public function checkCallbackAmount($orderId, $callbackAmount): bool
    {
        $amount = $this->orderRepository->getAmountByOrderId($orderId);
        $this->orderRepository->updateCallbackAmountByOrderId($orderId, $callbackAmount);
        // 比较金额
        if ($amount != $callbackAmount) {
            $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::CHECK_FAIL);
            Log::channel('pay')->error('amount not match');
            throw new RenderException(Code::AMOUNT_NOT_MATCH, 'Amount Not Match');
        }
        return true;
    }

    /**
     * @param string $orderId
     * @param string $payOrderNo
     * @param int $payChannel
     * @throws RenderException
     */
    public function updateOutOrderNo(string $orderId, string $payOrderNo, int $payChannel)
    {
        $this->orderRepository->updateOutOrderNoByOrderId($orderId, $payOrderNo, $payChannel);
    }

    /**
     * @param string $orderId
     * @throws RenderException
     */
    public function updateOrderSuccess(string $orderId)
    {
        Log::channel('pay')->info('deliver success');
        Log::channel('cp_notify')->info('deliver success');
        $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::DELIVER_SUCCESS);
    }

    /**
     * @param string $orderId
     * @throws RenderException
     */
    public function updateOrderDeliverFail(string $orderId)
    {
        Log::channel('pay')->info('deliver fail');
        Log::channel('cp_notify')->info('deliver fail');
        $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::DELIVER_FAIL);
    }

    /**
     * @param string $orderId
     * @throws RenderException
     */
    public function updateOrderPayFail(string $orderId)
    {
        Log::channel('pay')->info('pay fail, please check unifiedOrder');
        $this->orderRepository->updateStatusByOrderId($orderId, OrderStatus::PAY_FAIL);
    }
}
