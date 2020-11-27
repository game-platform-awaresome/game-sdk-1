<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TokenRequest;
use Illuminate\Http\Request;
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
        Log::channel('cp')->info('token validation param: ' . json_encode($request->all()));
        $result = $this->tokenService->checkToken();

        return $this->respJson($result);
    }
}
