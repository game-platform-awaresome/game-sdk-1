<?php
namespace App\Repositories;

use App\Constants\Os;
use App\Constants\UserType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\AccountLogin;
use App\Traits\AccountLoginRepositoryTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AccountLoginRepository
{
    use AccountLoginRepositoryTrait;

    /**
     * @var string
     */
    protected $tablePrefix = 'account_login_';

    /**
     * @var AccountLogin|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * OrderRepository constructor.
     */
    public function __construct()
    {
        $this->model = new AccountLogin();
        $this->checkTableExist($this->tablePrefix . date('Y'));
    }

    /**
     * 检查table是否存在
     *
     * @param $tableName
     */
    protected function checkTableExist($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            self::migrate($tableName);
        }
        $this->model->setTable($tableName);
    }

    /**
     * 记录日志
     *
     * @param $data
     * @return AccountLogin|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     */
    public function log(array $data)
    {
        try {
            return $this->model->create([
                'account_id' => $data['id'],
                'os' => $data['os'] ?? Os::Android,
                'user_type' => $data['user_type'],
                'device' => $data['device'],
                'app_id' => $data['app_id'],
                'ip' => $data['ip'],
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::LOGIN_LOG_RECORD_FAIL, 'Login log record fail');
        }
    }
}
