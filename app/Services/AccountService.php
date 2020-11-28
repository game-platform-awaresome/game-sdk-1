<?php
namespace App\Services;

use App\Constants\SmsType;
use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\IdentityRepository;
use App\Tools\HttpTool;
use App\Tools\StringTool;
use App\Repositories\AccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountService
{
    /**
     * @var string
     */
    protected $identifyUrl;

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
        $this->identifyUrl = config('services.identity.url');
        $this->accountRepository = new AccountRepository();
        $this->identityRepository = new IdentityRepository();
    }

    /**
     * 生成一个不存在于数据库中的新open_id
     *
     * @return mixed|string
     */
    protected function createOpenId()
    {
        $userId = StringTool::generateOpenId();
        if ($this->accountRepository->isOpenIdExist($userId)) {
            $userId = $this->createOpenId();
        }
        return $userId;
    }

    /**
     * 普通注册
     *
     * @param array $data
     * @throws RenderException
     */
    public function accountRegister(array $data)
    {
        $data['open_id'] = $this->createOpenId();
        $data['name'] = '用户' . time();
        $data['user_type'] = UserType::User;

        $this->accountRepository->createAccount($data);
    }

    /**
     * 游客注册
     *
     * @param array $data
     * @return bool
     * @throws RenderException
     */
    public function visitorRegister(array $data)
    {
        if (!$this->accountRepository->isUuidExist($data['uuid'])) {
            $data['phone'] = 00000000000;
            $data['name'] = '用户' . time();
            $data['open_id'] = $this->createOpenId();
            $data['password'] = StringTool::randomKey(10);
            $data['user_type'] = UserType::Visitor;
            $this->accountRepository->createAccount($data);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 游客绑定
     *
     * @param array $data
     * @throws RenderException
     */
    public function visitorBind(array $data)
    {
        // 更新手机号和密码
        $this->accountRepository->updatePhonePasswordByUuid($data['uuid'], $data);
        // 在根据手机号升级账号类型
        $this->accountRepository->updateUuidAndUserTypeByPhone($data['phone']);
    }

    /**
     * 修改密码
     *
     * @param string $phone
     * @param string $newPassword
     * @throws RenderException
     */
    public function changePassword(string $phone, string $newPassword)
    {
        $oldPassword = $this->accountRepository->getPasswordByPhone($phone);
        if (Hash::check($newPassword, $oldPassword)) {
            throw new RenderException(Code::THE_SAME_PASSWORD, 'The Same Password');
        }
        $this->accountRepository->updatePasswordByPhone($phone, $newPassword);
    }

    /**
     * 二要素实名认证
     *
     * @param array $data
     * @return \App\Models\Identity|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identifyIDInfoByTwoFactor(array $data)
    {
        if (!HttpTool::identifyTwoFactor($this->identifyUrl, $data)) {
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
        $accountId = $this->accountRepository->getIdByOpenId($data['open_id']);
        return $this->identityRepository->identity($accountId, $data);
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
        if ($this->identityRepository->isIdNumberAndIdNameExistByAccountId($accountId, $data['old_id_number'], $data['old_id_name'])) {
            Log::channel('sdk')->info('旧身份证信息与数据库不匹配');
            throw new RenderException(Code::ID_INFO_DOES_NOT_MATCH, 'ID info does not match');
        }
    }
}
