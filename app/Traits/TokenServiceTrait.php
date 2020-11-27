<?php

namespace App\Traits;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

trait TokenServiceTrait
{
    /**
     * create token for user
     *
     * @param array $credentials
     * @return string
     * @throws RenderException
     */
    protected function createTokenFromCredential(array $credentials) : string
    {
        if (! $token = Auth::guard('api')->attempt($credentials)) {
            throw new RenderException(Code::ACCOUNT_LOGIN_WRONG_PASSWORD, 'Authorization Failure');
        }

        return $token;
    }

    /**
     * create token from user
     *
     * @param Account $user
     * @return mixed
     * @throws RenderException
     */
    protected function createTokenFromAccount(Account $user) : string
    {
        if (! $token = Auth::guard('api')->fromUser($user)) {
            throw new RenderException(Code::VISITOR_LOGIN_FAIL, 'Authorization Failure');
        }

        return $token;
    }

    /**
     * 无效化token
     * @param string $token
     */
    protected function destroyToken(string $token) : void
    {
        // 当该token还有效时，对该token进行无效化处理
        if (JWTAuth::setToken($token)->check()) {
            JWTAuth::setToken($token)->invalidate();
        }
    }

    /**
     * refresh token
     *
     * @return string
     */
    protected function refreshToken() : string
    {
        return Auth::guard('api')->refresh();
    }
}