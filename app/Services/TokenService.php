<?php

namespace App\Services;

use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use App\Repositories\IdentityRepository;
use App\Repositories\AccountLoginRepository;
use App\Traits\TokenServiceTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService
{
    use TokenServiceTrait;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var AccountLoginRepository
     */
    protected $accountLoginRepository;

    /**
     * @var IdentityRepository
     */
    protected $identityRepository;

    /**
     * TokenService constructor.
     */
    public function __construct()
    {
        $this->accountRepository = new AccountRepository();
        $this->accountLoginRepository = new AccountLoginRepository();
        $this->identityRepository = new IdentityRepository();
    }

    /**
     * 手机号密码登录
     *
     * @param array $data
     * @return array
     * @throws RenderException
     */
    public function credentialLogin(array $data)
    {
        $credential = ['phone' => $data['phone'], 'password' => $data['password']];
        $token = $this->createTokenFromCredential($credential);
        // 销毁旧有效token
        $account = $this->accountRepository->getAccountByPhone($data['phone'])->toArray();
        if ($account['token']) {
            $this->destroyToken($account['token']);
        }
        $this->accountRepository->updateTokenByPhone($data['phone'], $token);
        // 日志记录
        $data['uuid'] = $account['uuid'];
        $data['open_id'] = $account['open_id'];
        $data['user_type'] = UserType::User;
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $data);
    }

    /**
     * UUID登录
     *
     * @param array $data
     * @return array
     * @throws RenderException
     */
    public function uuidLogin(array $data)
    {
        $account = $this->accountRepository->getAccountByUuid($data['uuid']);
        $token = $this->createTokenFromAccount($account);
        // 销毁旧有效token
        if ($account['token']) {
            $this->destroyToken($account['token']);
        }
        $this->accountRepository->updateTokenByUuid($data['uuid'], $token);
        // 日志记录
        $data['uuid'] = $account['uuid'];
        $data['phone'] = $account['phone'];
        $data['open_id'] = $account['open_id'];
        $data['user_type'] = UserType::Visitor;
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $data);
    }

    /**
     * token刷新
     * 一定是在登录后
     *
     * @param array $data
     * @return array
     * @throws RenderException
     */
    public function tokenRefresh(array $data)
    {
        $account = $this->accountRepository->getAccountByOpenId($data['open_id']);
        // 刷新token
        $token = $this->refreshToken();
        $this->accountRepository->updateTokenByOpenId($data['open_id'], $token);
        // 日志记录
        $data['uuid'] = $account['uuid'];
        $data['phone'] = $account['phone'];
        $data['open_id'] = $account['open_id'];
        $data['user_type'] = $account['user_type'];
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $data);
    }

    /**
     * @return mixed
     * @throws RenderException
     */
    public function checkToken()
    {
        try {
            $account = Auth::guard('api')->user();
            Log::channel('cp')->info("user open_id: " . $account['open_id']);
            return ['open_id' => $account['open_id'], 'name' => $account['name']];
        } catch (Exception $e) {
            throw new RenderException(Code::INVALID_TOKEN, 'Invalid Token');
        }
    }

    /**
     * 整理登录返回数据
     *
     * @param string $token
     * @param array $data
     * @return array
     */
    protected function responseWithToken(string $token, array $data) : array
    {
        $identity = $this->identityRepository->getIdentityByOpenId($data['open_id']);

        return [
            'token' => $token,
            'user_type' => $data['user_type'],
            'open_id' => $data['open_id'],
            'uuid' => $data['uuid'] ?? '',
            'id_number' => $identity['id_number'] ?? '',
            'id_name' => $identity['id_name'] ?? '',
            'birthday' => $identity['birthday'] ?? '',
            'age' => $identity['age'] ?? -1
        ];
    }
}
