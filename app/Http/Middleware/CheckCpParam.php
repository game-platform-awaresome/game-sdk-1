<?php


namespace App\Http\Middleware;


use App\Exceptions\Code;
use App\Exceptions\RenderException;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckCpParam
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
     * 检查 app_id 是否存在且不能为空
     *
     * @param $param
     * @throws RenderException
     */
    protected function checkNullable($param)
    {
        $validator = Validator::make($param, [
            'app_id' => 'required|string|exists:app_info',
        ]);

        if ($validator->fails()) {
            Log::channel('sdk')->error("必传参数：" . $validator->errors()->first());
            throw new RenderException(Code::WRONG_REQUEST_ATTRIBUTE, $validator->errors()->first());
        }
    }
}