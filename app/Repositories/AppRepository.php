<?php
namespace App\Repositories;

use App\Constants\CacheHeader;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class AppRepository
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
     * @var App|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * AppRepository constructor.
     * @param int $appId
     */
    public function __construct(int $appId)
    {
        $this->model = new App();
        $this->appId = $appId;
    }

    /**
     * @return array
     * @throws RenderException
     */
    public function getApp()
    {
        try {
            $appId = $this->appId;
            return Cache::remember(CacheHeader::APP_CONFIG . $this->appId, $this->ttl, function () use ($appId) {
                return $this->model->where('id', $appId)->firstOrFail()->toArray();
            });
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_APP_ID, 'Invalid APP ID');
        }
    }
}
