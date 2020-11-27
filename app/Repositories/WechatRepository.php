<?php


namespace App\Repositories;


use App\Constants\CacheHeader;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\Wechat;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class WechatRepository
{
    /**
     * @var float|int
     */
    protected $ttl = 60 * 60 * 12;

    /**
     * @var
     */
    protected $appId;

    /**
     * @var Wechat|\Illuminate\Database\Eloquent\Builder
     */
    protected $model;

    /**
     * WechatRepository constructor.
     * @param int $appId
     */
    public function __construct(int $appId)
    {
        $this->model = new Wechat();
        $this->appId = $appId;
    }

    /**
     * @return array|null
     * @throws RenderException
     */
    public function getConfig()
    {
        try {
            $appId = $this->appId;
            return Cache::remember(CacheHeader::WechatConfig . $appId, $this->ttl, function () use ($appId) {
                return $this->model->where('app_id', $appId)->firstOrFail()->toArray();
            });
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_APP_ID, 'Invalid APP ID');
        }
    }
}