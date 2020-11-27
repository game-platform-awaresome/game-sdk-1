<?php


namespace App\Http\Middleware;


use App\Exceptions\Code;
use App\Exceptions\RenderException;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckSdkParam
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws RenderException
     */
    public function handle($request, Closure $next)
    {
        $this->checkNullable($request->all());

        return $next($request);
    }

    /**
     * 检查 params app_id, uuid, channel_id, sub_chan_merchant, os, sign 是否存在且不能为空
     *
     * @param $param
     * @throws RenderException
     */
    protected function checkNullable($param)
    {
        $validator = Validator::make($param, [
            'app_id' => 'required|int|exists:app_info,app_id',
            'time' => 'required',
            'os' => 'required|int|in:1,2',
            'sign' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::channel('sdk')->error('Param miss: ' . $validator->errors()->first());
            throw new RenderException(Code::WRONG_REQUEST_ATTRIBUTE, $validator->errors()->first());
        }
    }
}