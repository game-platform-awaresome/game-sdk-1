<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TokenRequest;
use App\Services\TokenService;

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
        $result = $this->tokenService->checkToken($param);

        return $this->respJson($result);
    }
}
