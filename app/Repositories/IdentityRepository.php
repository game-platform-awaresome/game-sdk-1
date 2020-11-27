<?php
namespace App\Repositories;

use App\Exceptions\Code;
use App\Models\Identity;
use App\Exceptions\RenderException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class IdentityRepository
{
    /**
     * @var Identity|\Illuminate\Database\Eloquent\Builder
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
     * 根据openId获取实名信息
     * 用于登录的信息收集
     *
     * @param int $openId
     * @return Identity|bool|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder
     */
    public function getIdentityByOpenId(int $openId)
    {
        try {
            return $this->model->where('open_id', $openId)->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            Log::channel('sdk')->info('用户未实名');
            return null;
        }
    }

    /**
     * 判断姓名和手机号码是否正确
     * 判断旧身份用户
     *
     * @param string $openId
     * @param string $idNumber
     * @param string $idName
     * @return bool
     */
    public function isNumberAndNameExist(string $openId, string $idNumber, string $idName)
    {
        return $this->model->where([
            'open_id' => $openId,
            'id_number' => $idNumber,
            'id_name' => $idName
        ])->get()->isNotEmpty();
    }

    /**
     * 判断身份证号码是否存库
     *
     * @param int $id_number
     * @return bool
     */
    public function isIdNumberExist(int $id_number)
    {
        return $this->model->where('id_number', $id_number)->get()->isNotEmpty();
    }

    /**
     * 实名认证入库
     * 如果存在openId则更新，不存在则创建
     *
     * @param array $data
     * @return Identity|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     */
    public function identity(array $data)
    {
        try {
            $year = substr($data['id_number'], 6, 4);
            $month = substr($data['id_number'], 10, 2);
            $day = substr($data['id_number'], 12, 2);

            return $this->model->updateOrCreate([
                'open_id' => $data['open_id'],
            ],[
                'id_number' => $data['id_number'],
                'id_name' => $data['id_name'],
                'birthday' => $year . '-' . $month . '-' . $day
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            throw new RenderException(Code::IDENTIFY_FAIL, 'Identity Fail');
        }
    }
}
