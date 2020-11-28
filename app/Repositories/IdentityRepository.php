<?php
namespace App\Repositories;

use App\Exceptions\Code;
use App\Models\Identity;
use App\Exceptions\RenderException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Polyfill\Ctype\Ctype;

class IdentityRepository
{
    /**
     * @var Identity
     */
    protected $model;

    /**
     * IdentityRepository constructor.
     */
    public function __construct()
    {
        $this->model = new Identity();
    }

    /**
     * 根据account_id获取实名信息
     * 用于登录的信息收集
     *
     * @param int $accountId
     * @return Identity|bool|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder
     */
    public function getIdentityByAccountId(int $accountId)
    {
        try {
            return $this->model->where('account_id', $accountId)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            Log::channel('sdk')->info('用户未实名');
            return null;
        }
    }

    /**
     * 根据account_id获取身份证号码和名字
     * 身份证取出比对
     *
     * @param string $accountId
     * @return array|null
     * @throws RenderException
     */
    public function getIdNumberAndIdNameByAccountId(string $accountId)
    {
        try {
            return $this->model->where('account_id', $accountId)->firstOrFail(['id_number', 'id_name'])->toArray();
        } catch (ModelNotFoundException $exception) {
            Log::channel('sdk')->info('未实名');
            throw new RenderException(Code::ID_INFO_DOES_NOT_EXIST, 'ID info does not exist');
        }
    }

    /**
     * 判断身份证号码是否存库
     *
     * @param int $idNumber
     * @return bool
     */
    public function isIdNumberExist(int $idNumber)
    {
        return $this->model->where('id_number', $idNumber)->get()->isNotEmpty();
    }

    /**
     * 实名认证入库
     * 如果存在openId则更新，不存在则创建
     *
     * @param int $accountId
     * @param array $data
     * @return Identity|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     */
    public function identity(int $accountId, array $data)
    {
        try {
            $year = substr($data['id_number'], 6, 4);
            $month = substr($data['id_number'], 10, 2);
            $day = substr($data['id_number'], 12, 2);

            return $this->model->updateOrCreate([
                'account_id' => $accountId,
            ],[
                'id_number' => Crypt::encrypt($data['id_number']),
                'id_name' => Crypt::encrypt($data['id_name']),
                'birthday' => $year . '-' . $month . '-' . $day
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
    }
}
