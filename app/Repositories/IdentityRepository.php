<?php
namespace App\Repositories;

use App\Exceptions\Code;
use App\Models\Identity;
use App\Exceptions\RenderException;
use App\Tools\CryptTool;
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
     * 根据account_id、身份证号码和名字
     * 直接数据库查询 ，如果为空，意味着不匹配
     *
     * @param int $accountId
     * @param string $idNumber
     * @param string $idName
     * @return bool
     */
    public function isIdNumberAndIdNameExistByAccountId(int $accountId, string $idNumber, string $idName)
    {
        return $this->model->where([
            'account_id' => $accountId,
            'id_number' => CryptTool::encrypt($idNumber),
            'id_name' => CryptTool::encrypt($idName),
        ])->get()->isNotEmpty();
    }

    /**
     * 判断身份证号码是否存库
     *
     * @param string $idNumber
     * @return bool
     */
    public function isIdNumberExist(string $idNumber)
    {
        return $this->model->where('id_number', CryptTool::encrypt($idNumber))->get()->isNotEmpty();
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
                'id_number' => CryptTool::encrypt($data['id_number']),
                'id_name' => CryptTool::encrypt($data['id_name']),
                'birthday' => $year . '-' . $month . '-' . $day
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
    }
}
