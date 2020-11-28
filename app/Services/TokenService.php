<?php

namespace App\Services;

use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use App\Repositories\IdentityRepository;
use App\Repositories\AccountLoginRepository;
use App\Tools\StringTool;
use App\Traits\TokenServiceTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $account = $this->accountRepository->getAccountByPhone($data['phone']);
        if ($account['token']) {
            $this->destroyToken($account['token']);
        }
        $this->accountRepository->updateTokenByPhone($data['phone'], $token);
        // 日志记录
        $data['id'] = $account['id'];
        $data['user_type'] = UserType::User;
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $account->toArray());
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
        $data['id'] = $account['id'];
        $data['user_type'] = UserType::Visitor;
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $account->toArray());
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
        $data['id'] = $account['id'];
        $data['user_type'] = $account['user_type'];
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $account->toArray());
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
     * @param array $account
     * @return array
     */
    protected function responseWithToken(string $token, array $account) : array
    {
        $identity = $this->identityRepository->getIdentityByAccountId($account['id']);

        return [
            'token' => $token,
            'user_type' => $account['user_type'],
            'open_id' => $account['open_id'],
            'uuid' => $account['uuid'] ?? '',
            'id_number' => isset($identity['id_number']) ? StringTool::idNumberReplace($identity['id_number']) : '',
            'id_name' => isset($identity['id_name']) ? StringTool::idNameReplace($identity['id_name']) : '',
            'birthday' => $identity['birthday'] ?? '',
            'age' => $identity['age'] ?? -1
        ];
    }
}
