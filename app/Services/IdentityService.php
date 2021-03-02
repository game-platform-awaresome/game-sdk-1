<?php


namespace App\Services;


use App\Constants\IdentityStatus;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use App\Repositories\IdentityRepository;
use App\Tools\HttpTool;
use Illuminate\Support\Facades\Log;

class IdentityService
{
    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var IdentityRepository
     */
    protected $identityRepository;

    /**
     * AccountService constructor.
     */
    public function __construct()
    {
        $this->accountRepository = new AccountRepository();
        $this->identityRepository = new IdentityRepository();
    }

    /**
     * 实名认证
     *
     * @param array $data
     * @return \App\Models\Identity|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identify(array $data)
    {
        // 获取accountId
        $accountId = $this->accountRepository->getIdByOpenId($data['open_id']);

        // 判断是否在认证中，禁止多次提交
        $identity = $this->identityRepository->getIdentityByAccountId($accountId);
        if ($identity && $identity['status'] == IdentityStatus::AUTHING) {
            throw new RenderException(Code::IDENTIFY_ING, 'Identifying');
        }

        $identityResult = HttpTool::identify($data);
        if (!$identityResult) {
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }

        // 判断认证状态
        if ($identityResult['status'] != IdentityStatus::NO_AUTH) {
            $data['pi'] = $identityResult['pi'];
            $data['status'] = $identityResult['status'];
            return $this->identityRepository->identity($accountId, $data);
        } else {
            // 更新为空值
            $data['pi'] = null;
            $data['id_number'] = null;
            $data['id_name'] = null;
            $data['status'] = IdentityStatus::NO_AUTH;
            $this->identityRepository->identity($accountId, $data);
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
    }

    /**
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
}