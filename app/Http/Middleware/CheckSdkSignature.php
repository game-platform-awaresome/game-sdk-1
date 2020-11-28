<?php

namespace App\Http\Middleware;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AppRepository;
use App\Services\AppService;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;

class CheckSdkSignature
{
    // 请求过期时间
    const EXPIRES = 60 * 5;

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws RenderException
     */
    public function handle($request, Closure $next)
    {
        // 时间过期检查
        $this->checkTimeExpire($request->all());
        // 签名校验
        $this->checkSignature($request->all());

        return $next($request);
    }

    /**
     * 请求时间校验
     *
     * @param array $param
     * @throws RenderException
     */
    protected function checkTimeExpire(array $param)
    {
        if (abs(Carbon::now()->timestamp - $param['time']) > self::EXPIRES) {
            throw new RenderException(Code::TIME_EXPIRED, 'Time Expired');
        }
    }

    /**
     * 签名校验
     *
     * @param array $param
     * @throws RenderException
     */
    protected function checkSignature(array $param)
    {
        // 查找 appid 对应的 app_secret
        $app = new AppService($param['app_id']);
        $appSecret = $app->getSecret();
        // 记录sign并删除sign字段
        $sign = $param['sign'];
        unset($param['sign']);
        // 排序
        ksort($param);
        // 加上app_secret
        $param['app_secret'] = $appSecret;
        // 生成签名字符串
        array_walk($param, function(&$value, $key) {
            $value = "{$key}={$value}";
        });
        $str = implode('&', $param);
        // 比较签名
        if (md5($str) != $sign) {
            throw new RenderException(Code::SIGNATURE_FAIL, 'Signature Fail');
        }
    }
}
