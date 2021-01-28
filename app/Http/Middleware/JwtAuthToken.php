<?php

namespace App\Http\Middleware;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class JwtAuthToken
{
    /**
     * @var JWTAuth
     */
    protected $auth;

    /**
     * JwtAuthToken constructor.
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     * 不使用jwt自带的接口，是为了更方便对异常流程的控制
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     * @throws RenderException
     */
    public function handle($request, Closure $next)
    {
        try {
            if (! $this->auth->parser()->hasToken()) {
                throw new RenderException(Code::TOKEN_NOT_PROVIDED, 'Token Not Provided');
            }
            if (! $this->auth->parseToken()->authenticate()) {
                throw new RenderException(Code::TOKEN_NOT_FOUND_ACCOUNT, 'Token Not Found Account');
            }
        } catch (RenderException $exception) {
            throw new RenderException($exception->getCode(), $exception->getMessage());
        } catch (JWTException $exception) {
            try {
                if ($request->routeIs('api.token.refresh') && $token = $this->auth->refresh()) {
                    $request->merge(['new_token' => $token]);
                    return $next($request);
                }
            } catch (Exception $exception) {
                throw new RenderException(Code::INVALID_TOKEN, 'Invalid Token');
            }
            throw new RenderException(Code::INVALID_TOKEN, 'Invalid Token');
        }
        return $next($request);
    }
}
