<?php


namespace App\Providers;

use App\Constants\PayWay;
use App\Contracts\PayInterface;
use App\Services\AlipayService;
use App\Services\WechatService;
use Illuminate\Support\ServiceProvider;

class PayServiceProvider extends ServiceProvider
{
    /**
     * If is defer.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register services.
     * 将支付注册进去
     * 注册部分不要有对未知事物的依赖
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('pay.alipay', function () {
            return new AlipayService((int)request('app_id'));
        });

        $this->app->bind('pay.wechat', function () {
            return new WechatService((int)request('app_id'));
        });
    }

    /**
     * Bootstrap services.
     * 引导、初始化
     *
     * @return void
     */
    public function boot()
    {
        if ((int)request('pay_way') == PayWay::WECHAT) {
            $this->app->bind(PayInterface::class, 'pay.wechat');
        } else {
            $this->app->bind(PayInterface::class, 'pay.alipay');
        }
    }

    /**
     * Get services.
     * @return array
     */
    public function provides()
    {
        return ['pay.alipay', 'pay.wechat'];
    }
}