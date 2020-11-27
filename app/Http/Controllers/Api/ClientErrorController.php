<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Services\ClientErrorService;
use App\Http\Requests\ClientErrorRequest;

class ClientErrorController extends Controller
{
    /**
     * @var ClientErrorService
     */
    protected $clientErrorService;

    /**
     * ClientErrorController constructor.
     * @param ClientErrorService $clientErrorService
     */
    public function __construct(ClientErrorService $clientErrorService)
    {
        $this->clientErrorService = $clientErrorService;
    }

    /**
     * 客户端错误日志报告
     *
     * @param ClientErrorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function report(ClientErrorRequest $request)
    {
        $this->clientErrorService->info($request->all());
        return $this->respJson();
    }
}
