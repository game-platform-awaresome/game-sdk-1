<?php


namespace App\Http\Controllers\Api;

use App\Constants\PayWay;
use App\Exceptions\RenderException;
use App\Services\AlipayService;
use App\Services\AppService;
use App\Tools\HttpTool;
use App\Services\OrderService;
use App\Tools\SignTool;
use App\Services\WechatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wechat(Request $request)
    {
        try {
            // 等待Yansongda/pay扩展库升级微信接口的v3版本，使用json交互后，直接使用$request->all()
            $param = xmlToArray($request->getContent());
            Log::channel('pay')->info('wechat param: ' . var_export($param, true));
            $appId = json_decode(urldecode($param['attach']), true)['app_id'];
            $orderId = $param['out_trade_no'];

            // 初始化OrderService WechatService
            $payService = new WechatService($appId);
            $orderService = new OrderService($appId);

            // 验签
            $payService->notify();
            // 对 result_code 进行判断
            // 在微信的业务通知中，result_code 为交易标识，只有交易标识为 SUCCESS 时，微信才会认定为买家付款成功
            if ($param['result_code'] != 'SUCCESS') {
                $orderService->updateOrderPayFail($orderId);
            }
            // 判断订单状态
            $orderService->IsStatusInitOrPrepay($orderId);
            // 校验金额
            $orderService->checkCallbackAmount($orderId, $param['total_fee'] / 100);
            // 更新支付流水号
            $orderService->updateOutOrderNo($orderId, $param['transaction_id'], PayWay::WECHAT);
            // 获取回调信息并发送
            $this->callback($orderService, $appId, $orderId);
            // 返回结果
            return $this->respWechat();
        } catch (\Exception $exception) {
            // 捕捉所有异常，表示已收到消息，停止重发
            Log::channel('pay')->error('wechat error: ' . $exception->getMessage());
            return $this->respWechat();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function alipay(Request $request)
    {
        try {
            $param = $request->all();
            Log::channel('pay')->info('alipay param: ' . var_export($param, true));
            $appId = json_decode(urldecode($param['passback_params']), true)['app_id'];
            $orderId = $param['out_trade_no'];

            // 初始化OrderService AlipayService
            $payService = new AlipayService($appId);
            $orderService = new OrderService($appId);

            // 验签
            $payService->notify();
            // 对 trade_status 进行判断
            // 在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功
            if ($param['trade_status'] != 'TRADE_SUCCESS' && $param['trade_status'] != 'TRADE_FINISHED') {
                $orderService->updateOrderPayFail($orderId);
            }
            // 判断订单状态
            $orderService->IsStatusInitOrPrepay($orderId);
            // 校验金额
            $orderService->checkCallbackAmount($orderId, $param['total_amount']);
            // 更新支付流水号
            $orderService->updateOutOrderNo($orderId, $param['trade_no'], PayWay::ALIPAY);
            // 获取回调信息并发送
            $this->callback($orderService, $appId, $orderId);
            // 返回结果
            return $this->respAlipay();
        } catch (\Exception $exception) {
            // 捕捉所有异常，表示已收到消息，停止重发
            Log::channel('pay')->error('alipay error: ' . $exception->getMessage());
            return $this->respAlipay();
        }
    }

    /**
     * @param OrderService $orderService
     * @param string $appId
     * @param string $orderId
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function callback(OrderService $orderService, string $appId, string $orderId)
    {
        $appService = new AppService($appId);

        // 构建回调内容
        $notifyUrl = $appService->getNotifyUrl();
        $notifyOrderInfo = $orderService->getNotifyOrderInfo($orderId);
        $notifyOrderInfo['app_id'] = $appId;
        $secret = $appService->getSecret();

        $notifyOrderInfo = SignTool::generateSignToData($notifyOrderInfo, $secret);

        $result = HttpTool::notifyGame($notifyUrl, $notifyOrderInfo);
        if ($result == 'success') {
            $orderService->updateOrderSuccess($orderId);
        } else {
            $orderService->updateOrderDeliverFail($orderId);
        }
    }
}