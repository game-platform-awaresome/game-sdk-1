<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Code;
use App\Facades\Sms;
use App\Models\Account;
use App\Exceptions\RenderException;
use App\Http\Requests\SmsRequest;
use App\Handlers\VerificationCodeHandler;
use App\Services\SmsService;

class SmsController extends Controller
{
    /**
     * @var SmsService
     */
    protected $smsService;

    /**
     * SmsController constructor.
     * @param SmsService $smsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * @param SmsRequest $request
     * @return mixed
     * @throws RenderException
     * @throws \Exception
     */
    public function getValidationCode(SmsRequest $request)
    {
        $smsType = $request->input('sms_type');
        $phone = $request->input('phone');

        $this->smsService->isPhoneExist($smsType, $phone);
        $this->smsService->sendVerificationCode($phone);

        return $this->respJson();
    }

    /**
     * @param SmsRequest $request
     * @return mixed
     * @throws RenderException
     */
    public function checkValidationCode(SmsRequest $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');

        $this->smsService->checkVerificationCode($phone, $code);

        return $this->respJson();
    }
}
