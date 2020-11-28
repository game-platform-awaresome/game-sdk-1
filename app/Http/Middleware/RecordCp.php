<?php


namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RecordCp
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
        // 该中间件是第三方服务的日志中间件
        // 记录所有请求
        Log::channel('cp')->info("X-Request-ID: " . $request->header('X-Request-ID') .
            "\nPath: " . $request->getPathInfo() . " | ClientIp: " . $request->getClientIp() .
            "\nParam: " . json_encode($request->all()));

        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function terminate($request, $response)
    {
        Log::channel('cp')->info("X-Request-ID: " . $request->header('X-Request-ID') .
            "\nReturn: " . $response->getContent());
    }
}