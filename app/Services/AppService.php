<?php


namespace App\Services;

use App\Exceptions\RenderException;
use App\Repositories\AppRepository;

class AppService
{
    /**
     * @var AppRepository
     */
    protected $appRepository;

    /**
     * AppService constructor.
     * @param int $appId
     */
    public function __construct(int $appId)
    {
        $this->appRepository = new AppRepository($appId);
    }

    /**
     * @return string
     * @throws RenderException
     */
    public function getNotifyUrl()
    {
        return $this->appRepository->getApp()['notify_url'];
    }

    /**
     * @return mixed
     * @throws RenderException
     */
    public function getSecret()
    {
        return $this->appRepository->getApp()['secret'];
    }

    /**
     * @return mixed
     * @throws RenderException
     */
    public function getNotifyKey()
    {
        return $this->appRepository->getApp()['secret'];
    }
}