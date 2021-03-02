<?php

namespace App\Services;

use App\Constants\IdentityStatus;
use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use App\Repositories\IdentityRepository;
use App\Repositories\AccountLoginRepository;
use App\Tools\HttpTool;
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
        $data['user_type'] = UserType::USER;
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
        $data['user_type'] = UserType::VISITOR;
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
        $token = $data['token'];
        // 判断是否有新刷新的new_token，否则使用旧token（new_token有中间件产生）
        if (isset($data['new_token'])) {
            $token = $data['new_token'];
            $this->accountRepository->updateTokenByOpenId($data['open_id'], $token);
        }
        // 日志记录
        $data['id'] = $account['id'];
        $data['user_type'] = $account['user_type'];
        $this->accountLoginRepository->log($data);

        return $this->responseWithToken($token, $account->toArray());
    }

    /**
     * @param array $data
     * @return mixed
     * @throws RenderException
     */
    public function checkToken(array $data)
    {
        try {
            $account = Auth::guard('api')->user();
            return ['open_id' => $data['app_id'] . $account['open_id'], 'name' => $account['name']];
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws RenderException
     */
    protected function responseWithToken(string $token, array $account) : array
    {
        $identity = $this->identityRepository->getIdentityByAccountId($account['id'])->toArray();
        // 认证状态为认证中，每次登录请求都会更新一次认证状态
        if ($identity && $identity['status'] == IdentityStatus::AUTHING) {
            // 发起认证查询
            $identifyQueryResult = HttpTool::identifyQuery($identity['account_id']);
            // 认证成功/失败
            if ($identifyQueryResult['status'] == IdentityStatus::SUCCESS) {
                // 更新状态
                $identity['pi'] = $identifyQueryResult['pi'];
                $identity['status'] = $identifyQueryResult['status'];
                $this->identityRepository->identity($account['id'], $identity);
            } elseif ($identifyQueryResult['status'] == IdentityStatus::NO_AUTH) {
                // 更新为空值
                $identity['pi'] = null;
                $identity['id_number'] = null;
                $identity['id_name'] = null;
                $identity['status'] = IdentityStatus::NO_AUTH;
                $this->identityRepository->identity($account['id'], $identity);
            }
        }

        return [
            'token' => $token,
            'user_type' => $account['user_type'],
            'open_id' => $account['open_id'],
            'uuid' => $account['uuid'] ?? '',
            'id_number' => $identity['id_number'] ?? '',
            'id_name' => $identity['id_name'] ?? '',
            'birthday' => $identity['birthday'] ?? '',
            'age' => $identity['age'] ?? -1,
            'pi' => $identity['pi'] ?? '',
            'identity_status' => $identity['status'] ?? IdentityStatus::NO_AUTH,
        ];
    }
}
