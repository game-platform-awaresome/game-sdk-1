<?php

namespace App\Repositories;

use App\Constants\Os;
use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountRepository
{
    /**
     * @var Account
     */
    protected $model;

    /**
     * AccountRepository constructor.
     */
    public function __construct()
    {
        $this->model = new Account();
    }

    /**
     * 根据uuid寻找账号
     *
     * @param string $uuid
     * @return Account|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws RenderException
     */
    public function getAccountByUuid(string $uuid)
    {
        try {
            return $this->model->where('uuid', $uuid)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_UUID, 'Invalid UUID');
        }
    }

    /**
     * 根据手机号码寻找账号
     *
     * @param string $phone
     * @return Account|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws RenderException
     */
    public function getAccountByPhone(string $phone)
    {
        try {
            return $this->model->where('phone', $phone)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, 'Phone Number Unregistered');
        }
    }

    /**
     * 根据open_id寻找账号
     *
     * @param string $openId
     * @return Account|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     * @throws RenderException
     */
    public function getAccountByOpenId(string $openId)
    {
        try {
            return $this->model->where('open_id', $openId)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_OPEN_ID, 'Invalid OPEN ID');
        }
    }

    /**
     * 根据open_id寻找账号主键
     *
     * @param string $openId
     * @return string
     * @throws RenderException
     */
    public function getAccountIdByOpenId(string $openId)
    {
        try {
            return $this->model->where('open_id', $openId)->firstOrFail('id')->id;
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_OPEN_ID, 'Invalid OPEN ID');
        }
    }

    /**
     * 根据phone寻找账号的密码
     * 用于对照新旧密码
     *
     * @param string $phone
     * @return string
     * @throws RenderException
     */
    public function getPasswordByPhone(string $phone)
    {
        try {
            return $this->model->where('phone', $phone)->firstOrFail('password')->password;
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, 'Phone Number Unregistered');
        }
    }

    /**
     * 判断open_id是否存在
     * 存在则为真
     * 不存在为假
     *
     * @param string $account_id
     * @return bool
     */
    public function isOpenIdExist(string $account_id)
    {
        return $this->model->where('open_id', $account_id)->get()->isNotEmpty();
    }

    /**
     * 判断uuid是否存在
     * 存在则为真
     * 不存在为假
     *
     * @param string $uuid
     * @return bool
     */
    public function isUuidExist(string $uuid)
    {
        return $this->model->where('uuid', $uuid)->get()->isNotEmpty();
    }

    /**
     * 判断phone是否存在
     * 存在则为真
     * 不存在为假
     *
     * @param string $phone
     * @return bool
     */
    public function isPhoneExist(string $phone)
    {
        return $this->model->where('phone', $phone)->get()->isNotEmpty();
    }

    /**
     * 根据phone更新密码
     * 用于找回密码
     *
     * @param string $phone
     * @param string $password
     * @return mixed|string
     * @throws RenderException
     */
    public function updatePasswordByPhone(string $phone,string $password){
        try {
            return $this->model->where('phone', $phone)->update([
                'password' => Hash::make($password)
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, 'Phone Number Unregistered');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 根据uuid更新手机号和密码
     * 用于游客绑定
     *
     * @param string $uuid
     * @param array $data
     * @return mixed|string
     * @throws RenderException
     */
    public function updatePhonePasswordByUuid(string $uuid, array $data)
    {
        try {
            return $this->model->where('uuid', $uuid)->update([
                'phone' => $data['phone'],
                'password' => Hash::make($data['password'])
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_UUID, 'Invalid UUID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 根据手机号去除uuid和升级userType
     * 用于游客绑定
     *
     * @param string $phone
     * @return mixed|string
     * @throws RenderException
     */
    public function updateUuidAndUserTypeByPhone(string $phone)
    {
        try {
            return $this->model->where('phone', $phone)->update([
                'uuid' => null,
                'user_type' => UserType::User
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_UUID, 'Invalid UUID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 根据uuid更新Token
     *
     * @param string $uuid
     * @param string $token
     * @return mixed|string
     * @throws RenderException
     */
    public function updateTokenByUuid(string $uuid, string $token){
        try {
            return $this->model->where('uuid', $uuid)->update([
                'token' => $token,
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_UUID, 'Invalid UUID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 根据open_id更新Token
     *
     * @param string $openId
     * @param string $token
     * @return mixed|string
     * @throws RenderException
     */
    public function updateTokenByOpenId(string $openId, string $token){
        try {
            return $this->model->where('open_id', $openId)->update([
                'token' => $token,
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid OPEN ID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 根据手机号码更新Token
     *
     * @param string $uuid
     * @param string $token
     * @return mixed|string
     * @throws RenderException
     */
    public function updateTokenByPhone(string $uuid, string $token){
        try {
            return $this->model->where('phone', $uuid)->update([
                'token' => $token,
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, 'Phone Number Unregistered');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ACCOUNT_FAIL, 'Account Update Fail');
        }
    }

    /**
     * 创建账号
     *
     * @param array $data
     * @return Account|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     */
    public function createAccount(array $data)
    {
        try {
            return $this->model->create([
                'uuid' => $data['uuid'] ?? null,
                'open_id' => $data['open_id'],
                'name' => $data['name'] ?? $data['open_id'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'os' => $data['os'] ?? Os::Android,
                'user_type' => $data['user_type'] ?? UserType::User,
                'device' => $data['device'],
                'app_id' => $data['app_id'],
                'ip' => $data['ip'],
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::ACCOUNT_REGISTERED_FAIL, 'Account Registered Fail');
        }
    }
}
