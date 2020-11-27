<?php

namespace App\Http\Controllers\Api;

use App\Contracts\PayInterface;
use App\Exceptions\RenderException;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * OrderController constructor.
     * @throws RenderException
     */
    public function __construct()
    {
        $this->orderService = new OrderService((int)request('app_id'));
    }

    /**
     * 生成订单
     *
     * @param OrderRequest $request
     * @return mixed
     * @throws RenderException
     */
    public function store(OrderRequest $request)
    {
        $params = $request->all();
        $params['ip'] = $request->getClientIp();
        $orderId = $this->orderService->createOrder($params);

        return $this->respJson(['order_id' => $orderId]);
    }

    /**
     * 取消订单
     *
     * @param OrderRequest $request
     * @return mixed
     * @throws \Exception
     */
    public function update(OrderRequest $request)
    {
        $orderId = $request->input('order_id');
        // 检查订单状态
        $this->orderService->IsStatusInitOrPrepay($orderId);
        $this->orderService->cancelPay($orderId);

        return $this->respJson();
    }

    /**
     * 下单
     *
     * @param OrderRequest $request
     * @param PayInterface $pay
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function unifiedOrder(OrderRequest $request, PayInterface $pay)
    {
        $orderId = $request->input('order_id');
        $payWay = $request->input('pay_way');
        // 检查订单状态
        $this->orderService->IsStatusInitOrPrepay($orderId);
        // 下单，框架根据pay_way参数自动实例化WechatService或AlipayService
        // 具体可以查看PayServiceProvider
        $orderInfo = $this->orderService->getOrderInfo($orderId);
        $result = $pay->unifiedOrder($orderInfo);
        // 更新订单支付渠道和订单状态
        $this->orderService->unifiedOrder($orderId, $payWay);

        return $this->respJson($result);
    }

    /**
     * 查看订单状态
     *
     * @param OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function orderStatus(OrderRequest $request)
    {
        $orderId = $request->input('order_id');
        $orderStatus = $this->orderService->getOrderStatus($orderId);

        return $this->respJson(['order_status' => $orderStatus]);
    }

}
