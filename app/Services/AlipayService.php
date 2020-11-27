<?php


namespace App\Services;


use App\Contracts\PayInterface;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AlipayRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WechatRepository;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Exceptions\Exception;
use Yansongda\Pay\Exceptions\InvalidArgumentException;
use Yansongda\Pay\Exceptions\InvalidConfigException;
use Yansongda\Pay\Exceptions\InvalidSignException;
use Yansongda\Pay\Pay;

class AlipayService implements PayInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var AlipayRepository
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
        $this->configRepository = new AlipayRepository($appId);
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
            'app_id' => $config['open_id'],
            'notify_url' => route('api.pay.alipay.notify'),
            'ali_public_key' => $config['ali_public_key'],
            'private_key' => $config['private_key'],
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
            'subject' => '游戏充值-' . $itemName,
            'out_trade_no' => $orderId,
            'total_amount' => (float)$totalFee,
            'passback_params' => urlencode(json_encode(['app_id' => $this->appId])),
            'goods_type' => 0
        ];
    }

    /**
     * @param array $data
     * @return array
     * @throws RenderException
     */
    public function unifiedOrder(array $data)
    {
        $payOrder = $this->generatePayData($data['item_name'], $data['order_id'], $data['amount']);

        $pay = Pay::alipay($this->config);
        try {
            $payInfo = $pay->app($payOrder);
            return ['alipay' => $payInfo->getContent()];
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            throw new RenderException(Code::ALIPAY_SERVICE_ERROR, 'Alipay Service Error');
        }
    }

    /**
     * @return \Yansongda\Pay\Gateways\Alipay
     * @throws RenderException
     */
    public function notify()
    {
        try {
            $pay = Pay::alipay($this->config);
            $pay->verify();
            return $pay;
        } catch (InvalidSignException $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::ALIPAY_VERIFY_SIGN_FAIL, 'Alipay Verify Sign Error');
        } catch (InvalidConfigException $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::ALIPAY_INVALID_CONFIG, 'Alipay Invalid Config');
        } catch (Exception $exception) {
            Log::channel('pay')->error($exception->getMessage());
            throw new RenderException(Code::ALIPAY_SERVICE_ERROR, 'Alipay Service Error');
        }
    }

    /**
     * @param string $outTradeNo
     * @return array
     * @throws InvalidConfigException
     * @throws InvalidSignException
     * @throws \Yansongda\Pay\Exceptions\GatewayException
     */
    public function findOutTradeOrder(string $outTradeNo)
    {
        return Pay::alipay($this->config)->find($outTradeNo)->toArray();
    }
}