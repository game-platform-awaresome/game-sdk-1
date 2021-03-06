<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Http\Requests\AccountRequest;
use App\Repositories\IdentityRepository;
use App\Services\AccountService;
use App\Services\TokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var TokenService
     */
    protected $tokenService;

    /**
     * AccountController constructor.
     * @param AccountService $accountService
     * @param TokenService $tokenService
     */
    public function __construct(AccountService $accountService, TokenService $tokenService)
    {
        $this->accountService = $accountService;
        $this->tokenService = $tokenService;
    }

    /**
     * 游客注册&登录（如果无此uuid则直接注册后登录）
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function visitorLogin(Request $request)
    {
        $param = $request->all();
        $param['ip'] = $request->getClientIp();
        // 如果无此uuid则直接注册, 如果有直接跳过
        $this->accountService->visitorRegister($param);
        // 登录
        $data = $this->tokenService->uuidLogin($param);

        return $this->respJson($data);
    }

    /**
     * 账户注册
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function accountRegister(AccountRequest $request)
    {
        $param = $request->all();
        $param['ip'] = $request->getClientIp();
        $this->accountService->accountRegister($param);

        return $this->respJson();
    }

    /**
     * 手机密码登录
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function accountLogin(AccountRequest $request)
    {
        $param = $request->all();
        $param['ip'] = $request->getClientIp();
        // 登录
        $data = $this->tokenService->credentialLogin($param);

        return $this->respJson($data);
    }

    /**
     * 游客绑定
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function visitorBind(AccountRequest $request)
    {
        $param = $request->all();
        $param['ip'] = $request->getClientIp();
        // 进行绑定，将游客账号升级成正常用户 user_type = 0，uuid=null
        $this->accountService->visitorBind($param);
        // 登录
        $data = $this->tokenService->credentialLogin($param);

        return $this->respJson($data);
    }


    /**
     * token刷新(Header 需要 Bearer auth认证）
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function tokenRefresh(AccountRequest $request)
    {
        $param = $request->all();
        $param['ip'] = $request->getClientIp();
        // 打印旧token
        Log::channel('sdk')->info('old token: ' . $request->header('Authorization', '?'));
        // 刷新token
        $data = $this->tokenService->tokenRefresh($param);

        return $this->respJson($data);
    }

    /**
     * 找回密码
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     */
    public function findPassword(AccountRequest $request)
    {
        $phone = $request->input('phone');
        $password = $request->input('password');
        $this->accountService->changePassword($phone, $password);

        return $this->respJson();
    }

    /**
     * 实名认证
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bindIdentity(AccountRequest $request)
    {
        $param = $request->all();
        // 判断身份证号是否已存在
        $this->accountService->isIdNumberExist($param['id_number']);
        // 二要素实名认证
        $identity = $this->accountService->identifyIDInfoByTwoFactor($param)->toArray();

        return $this->respJson($identity);
    }

    /**
     * 实名换绑
     *
     * @param AccountRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeIdentity(AccountRequest $request)
    {
        $param = $request->all();

        // 判断新身份证号是否已存在
        $this->accountService->isIdNumberExist($param['id_number']);
        // 判断旧身份证信息是否匹配
        $this->accountService->checkOldIdentityMatch($param);
        // 二要素实名认证
        $identity = $this->accountService->identifyIDInfoByTwoFactor($param)->toArray();

        return $this->respJson($identity);
    }
}
