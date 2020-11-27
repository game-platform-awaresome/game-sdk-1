<?php


namespace App\Services;


use App\Exceptions\RenderException;
use App\Repositories\AppInfoRepository;
use Illuminate\Support\Facades\Log;

class AppInfoService
{
    /**
     * @var AppInfoRepository
     */
    protected $appInfoRepository;

    /**
     * AppInfoService constructor.
     * @param int $appId
     */
    public function __construct(int $appId)
    {
        $this->appInfoRepository = new AppInfoRepository($appId);
    }

    /**
     * @return string
     * @throws RenderException
     */
    public function getNotifyUrl()
    {
        return $this->appInfoRepository->getAppInfo()['notify_url'];
    }

    /**
     * @return mixed
     * @throws RenderException
     */
    public function getAppSecret()
    {
        return $this->appInfoRepository->getAppInfo()['app_secret'];
    }
}