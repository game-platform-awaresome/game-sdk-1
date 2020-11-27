<?php
namespace App\Repositories;

use App\Constants\CacheHeader;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\AppInfo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class AppInfoRepository
{
    /**
     * @var int
     */
    protected $appId;

    /**
     * @var int
     */
    protected $ttl = 60 * 60 * 12;

    /**
     * @var AppInfo|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * AppInfoRepository constructor.
     * @param int $appId
     */
    public function __construct(int $appId)
    {
        $this->model = new AppInfo();
        $this->appId = $appId;
    }

    /**
     * @return array
     * @throws RenderException
     */
    public function getAppInfo()
    {
        try {
            $appId = $this->appId;
            return Cache::remember(CacheHeader::AppConfig . $this->appId, $this->ttl, function () use ($appId) {
                return $this->model->where('app_id', $appId)->firstOrFail()->toArray();
            });
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_APP_ID, 'Invalid APP ID');
        }
    }
}
