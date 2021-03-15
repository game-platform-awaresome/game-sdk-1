<?php


namespace App\Http\Controllers\Api;


use App\Exceptions\RenderException;
use App\Services\WlcService;
use Illuminate\Http\Request;

class WlcController extends Controller
{

    /**
     * @var WlcService
     */
    protected $wlcService;

    /**
     * AccountController constructor.
     * @throws RenderException
     */
    public function __construct()
    {
        $this->wlcService = new WlcService((int)request('app_id'));
    }

    /**
     * 用户行为上报
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function behavioralReport(Request $request)
    {
        $param = $request->all();


        return $this->respJson();
    }
}