<?php


namespace App\Services;


use App\Constants\IdentityStatus;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use App\Repositories\AppRepository;
use App\Repositories\IdentityRepository;
use App\Tools\CryptTool;
use App\Tools\HttpTool;
use App\Tools\SignTool;
use App\Tools\StringTool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WlcService
{
    /**
     * 认证失败次数（单位：次）
     * 认证失败时间（单位：小时）
     * 即 在指定时间内认证失败次数大于指定失败次数，指定时间内不允许再次认证
     */
    const identityFailFreq = 3;
    const identityTime = 24;

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var IdentityRepository
     */
    protected $identityRepository;

    /**
     * @var AppRepository
     */
    protected $appRepository;

    /**
     * @var string
     * 国家防沉迷 app_id
     */
    protected $wlcAppId;

    /**
     * @var string
     * 国家防沉迷 secret
     */
    protected $wlcAppSecret;

    /**
     * @var string
     * 国家防沉迷 游戏备案识别码
     */
    protected $bizId;

    /**
     * AccountService constructor.
     * @param int $appId
     * @throws RenderException
     */
    public function __construct(int $appId)
    {
        $this->accountRepository = new AccountRepository();
        $this->identityRepository = new IdentityRepository();
        $this->appRepository = new AppRepository($appId);

        $this->wlcAppId = config('services.wlc.app_id');
        $this->wlcAppSecret = config('services.wlc.app_secret');
        $this->bizId = $this->appRepository->getApp()['biz_id'];
    }

    /**
     * 首次实名认证 和 更换实名认证
     *
     * @param array $data
     * @return \App\Models\Identity|array|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identify(array $data)
    {
        // 获取认证失败次数缓存
        $cacheKey = 'identity_fail_freq:' . $data['open_id'];
        $identityFailFreq = Cache::get($cacheKey, 0);

        // 判断认证失败次数是否超过限制
        if ($identityFailFreq >= self::identityFailFreq) {
            Cache::increment($cacheKey, 1);
            throw new RenderException(Code::IDENTIFY_FAIL_MANY, 'Too many identity fail');
        }

        // 获取accountId
        $accountId = $this->accountRepository->getIdByOpenId($data['open_id']);

        // 判断是否在认证中，禁止多次提交
        $identity = $this->identityRepository->getIdentityByAccountId($accountId);
        if ($identity && $identity['status'] == IdentityStatus::AUTHING) {
            throw new RenderException(Code::IDENTIFY_ING, 'Identifying');
        }

        $identityResult = $this->wlcAuthenticationCheck($data);
        if (!$identityResult) {
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }

        // 判断认证状态
        if ($identityResult['status'] != IdentityStatus::NO_AUTH) {
            // 更新状态
            $data['pi'] = $identityResult['pi'];
            $data['status'] = $identityResult['status'];
            // 隐藏真实姓名
            $result = $this->identityRepository->identity($accountId, $data);
            $result->addHidden(['replace_id_number', 'replace_id_name']);
            $resultArray = $result->toArray();
            // 覆盖和隐藏真实姓名
            $resultArray['id_number'] = $result->replace_id_number;
            $resultArray['id_name'] = $result->replace_id_name;
            return $resultArray;
        } else {
            // 认证失败 记录次数
            if (!$identityFailFreq) {
                Cache::add($cacheKey, 1, now()->addHours(self::identityTime));
            } else {
                Cache::increment($cacheKey, 1);
            }
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
    }

    /**
     * 实名认证查询
     *
     * @param array $data
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identifyQuery(array $data)
    {
        // 获取accountId
        if (isset($data['open_id'])) {
            $accountId = $this->accountRepository->getIdByOpenId($data['open_id']);
        } else {
            $account = $this->accountRepository->getAccountByPhone($data['phone']);
            $accountId = $account['id'];
            $data['open_id'] = $account['open_id'];
        }

        // 判断是否在认证中
        $identity = $this->identityRepository->getIdentityByAccountId($accountId);

        // 认证状态为认证中，每次登录请求都会更新一次认证状态
        if ($identity && $identity['status'] == IdentityStatus::AUTHING) {
            $identity = $identity->toArray();
            // 发起认证结果查询
            if ($identityQueryResult = $this->wlcAuthenticationQuery($data)) {
                // 认证成功/失败
                // 还处于认证中，不处理
                switch ($identityQueryResult['status']) {
                    case IdentityStatus::SUCCESS:
                        // 更新认证状态
                        $identity['pi'] = $identityQueryResult['pi'];
                        $identity['status'] = $identityQueryResult['status'];
                        $this->identityRepository->identity($accountId, $identity);
                        break;
                    case IdentityStatus::NO_AUTH:
                        // 更新为空值
                        $identity['pi'] = null;
                        $identity['id_number'] = null;
                        $identity['id_name'] = null;
                        $identity['status'] = IdentityStatus::NO_AUTH;
                        $this->identityRepository->identity($accountId, $identity);
                        break;
                }
            }
        }
    }

    /**
     *
     *
     * @param string $idNumber
     * @throws RenderException
     */
    public function isIdNumberExist(string $idNumber)
    {
        if ($this->identityRepository->isIdNumberExist($idNumber)) {
            throw new RenderException(Code::ID_INFO_ALREADY_EXIST, 'ID number already exist');
        }
    }

    /**
     * 检查数据库旧身份证号码是否匹配
     *
     * @param array $data
     * @throws RenderException
     */
    public function checkOldIdentityMatch(array $data)
    {
        $accountId = $this->accountRepository->getIdByOpenId($data['open_id']);
        if (!$this->identityRepository->isIdNumberAndIdNameExistByAccountId($accountId, $data['old_id_number'], $data['old_id_name'])) {
            Log::channel('sdk')->info('旧身份证信息与数据库不匹配');
            throw new RenderException(Code::ID_INFO_DOES_NOT_MATCH, 'ID info does not match');
        }
    }

    /**
     * 实名认证接口
     * 参数整理
     *
     * @param array $data
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function wlcAuthenticationCheck(array $data)
    {
        // 地址
        $url = config('services.wlc.authentication_check_url');
        // 原始报文体/加密报文体
        $body = ['ai' => $data['open_id'], 'name' => $data['id_name'], 'idNum' => $data['id_number']];
        $body = ['data' => CryptTool::aes128gcm($body)];
        // 报文头
        $headers = ['appId' => $this->wlcAppId, 'bizId' => $this->bizId, 'timestamps' => StringTool::microtime()];
        // 报文头生成签名
        $headers['sign'] = SignTool::generateWlcSign($this->wlcAppSecret, $headers, null, $body);

        return HttpTool::identify($url, $headers, $body);
    }

    /**
     * 实名认证查询接口
     * 参数整理
     *
     * @param array $data
     * @return bool|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function wlcAuthenticationQuery(array $data)
    {
        // 地址
        $url = config('services.wlc.authentication_query_url');
        // 查询字符串
        $query = ['ai' => $data['open_id']];
        // 报文头
        $headers = ['appId' => $this->wlcAppId, 'bizId' => $this->bizId, 'timestamps' => StringTool::microtime()];
        // 报文头生成签名
        $headers['sign'] = SignTool::generateWlcSign($this->wlcAppSecret, $headers, $query);

        return HttpTool::identify($url, $headers, $query, 'get');
    }
}