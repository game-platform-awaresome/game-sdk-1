<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TokenRequest;
use App\Services\TokenService;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * TokenController constructor.
     * @param TokenService $tokenService
     */
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Token校验
     *
     * @param TokenRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\RenderException
     */
    public function validateToken(TokenRequest $request)
    {
        $param = $request->all();
        Log::channel('cp')->info('token validation param: ' . json_encode($param));
        $result = $this->tokenService->checkToken($param);

        return $this->respJson($result);
    }
}
