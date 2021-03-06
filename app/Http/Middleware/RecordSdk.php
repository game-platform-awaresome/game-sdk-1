<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RecordSdk
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        // 该中间件是SDK客户端，API通道请求的日志中间件
        // 记录所有请求
        Log::channel('sdk')->info("x-request-id: " . $request->header('X-Request-ID') .
            "\nparam: " . var_export($request->all(), true) .
            " path: " . $request->getPathInfo() . " | ip: " . $request->getClientIp());

        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function terminate($request, $response)
    {
        Log::channel('sdk')->info("x-request-id: " . $request->header('X-Request-ID') .
            "\nresponse: " . $response->getContent());
    }
}