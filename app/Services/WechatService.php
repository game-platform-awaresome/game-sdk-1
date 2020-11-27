<?php


namespace App\Services;

use App\Contracts\PayInterface;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\WechatRepository;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Exceptions\Exception;
use Yansongda\Pay\Exceptions\InvalidArgumentException;
use Yansongda\Pay\Exceptions\InvalidSignException;
use Yansongda\Pay\Pay;

class WechatService implements PayInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var WechatRepository
     */
    protected $configRepository;

    /**
     * @var int
     */
    protected $appId;

    /**
     * WechatService constructor.
     * @param int $appId
     * @throws RenderException
     */
    public function __construct(int $appId)
    {
        $this->configRepository = new WechatRepository($appId);
        $this->appId = $appId;
        // 初始化参数
        $this->initConfig();
    }

    /**
     * @throws RenderException
     */
    protected function initConfig()
    {
        $config = $this->configRepository->getConfig();
        $this->config = [
            'appid' => $config['open_id'],
            'mch_id' => $config['mch_id'],
            'notify_url' => route('api.pay.wechat.notify'),
            'key' => $config['app_secret'],
            'log' => [
                'file' => storage_path('logs/pay/unifiedOrder.log'),
                'level' => config('app.debug') ? 'debug' : 'info',
                'type' => 'daily',
            ]
        ];
    }

    /**
     * @param string $itemName
     * @param string $orderId
     * @param $totalFee
     * @return array
     */
    protected function generatePayData(string $itemName, string $orderId, $totalFee)
    {
        return [
            'body' => '游戏充值-' . $itemName,
            'out_trade_no' => $orderId,
            'total_fee' => (float)($totalFee * 100), // 单位是分
            'attach' => urlencode(json_encode(['app_id' => $this->appId]))
        ];
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws RenderException
     */
    public function unifiedOrder(array $data)
    {
        $payOrder = $this->generatePayData($data['item_name'], $data['order_id'], $data['amount']);

        $pay = Pay::wechat($this->config);
        try {
            $payInfo = $pay->app($payOrder);
            return json_decode($payInfo->getContent(), true);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            throw new RenderException(Code::WECHAT_SERVICE_ERROR, 'Wechat Service Error');
        }
    }

    /**
     * @return \Yansongda\Pay\Gateways\Wechat
     * @throws RenderException
     */
    public function notify()
    {
        try {
            $pay = Pay::wechat($this->config);
            $pay->verify();
            return $pay;
        } catch (InvalidSignException $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::WECHAT_VERIFY_SIGN_FAIL, 'Wechat Verify Sign Error');
        } catch (InvalidArgumentException $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::WECHAT_INVALID_ARGUMENT, 'Wechat Invalid Argument');
        } catch (Exception $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::WECHAT_SERVICE_ERROR, 'Wechat Service Error');
        }
    }

    /**
     * @param string $outTradeNo
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     * @throws \Yansongda\Pay\Exceptions\GatewayException
     */
    public function findOutTradeOrder(string $outTradeNo)
    {
        return Pay::wechat($this->config)->find($outTradeNo)->toArray();
    }
}