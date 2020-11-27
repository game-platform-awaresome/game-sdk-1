<?php


namespace App\Services;

use App\Constants\CacheHeader;
use App\Constants\SmsType;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Repositories\AccountRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\Exception;

class SmsService
{
    /**
     * 腾讯云的短信模板id
     */
    const qCloudTemplate = '340675';

    /**
     * uCloud的短信模板id
     */
    const uCloudTemplate = 'SIG202011267B7A72';

    /**
     * @var AccountRepository
     */
    protected $accountRepository;

    /**
     * @var EasySms
     */
    protected $easySms;

    /**
     * AccountService constructor.
     * @param EasySms $easySms
     */
    public function __construct(EasySms $easySms)
    {
        $this->accountRepository = new AccountRepository();
        $this->easySms = $easySms;
    }

    /**
     * 根据短信类型判断phone 是否该存在
     *
     * @param int $smsType
     * @param string $phone
     * @throws RenderException
     */
    public function isPhoneExist(int $smsType, string $phone)
    {
        $exist = $this->accountRepository->isPhoneExist($phone);
        if ($smsType == SmsType::Bind && $exist) {
            throw new RenderException(Code::PHONE_NUMBER_REGISTERED, 'Phone Number Registered');
        } else if ($smsType == SmsType::Register && $exist) {
            throw new RenderException(Code::PHONE_NUMBER_REGISTERED, 'Phone Number Registered');
        } else if ($smsType == SmsType::FindPassword && !$exist) {
            throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, 'Phone Number Unregistered');
        }
    }

    /**
     * @param string $phone
     * @throws \Exception
     */
    public function sendVerificationCode(string $phone)
    {
        $cacheKey = CacheHeader::Phone . $phone;
        if (Cache::get($cacheKey)) {
            throw new RenderException(Code::SMS_SENT, 'Sms sent');
        }
        $verificationCode = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT);
        // 发送验证码
        $this->store($phone, $verificationCode);
        // 发送成功后写入缓存Redis
        Cache::put($cacheKey, ['code' => $verificationCode], now()->addMinutes(10));
    }

    /**
     * @param string $phone
     * @param string $verificationCode
     * @throws RenderException
     */
    protected function store(string $phone, string $verificationCode)
    {
        try {
            Log::channel('third')->info('Sms Param: ' . json_encode(['phone' => $phone, 'code' => $verificationCode]));
            $this->easySms->send($phone, [
                'template' => function ($gateway) {
                    if ($gateway->getName() == 'ucloud') {
                        return self::uCloudTemplate;
                    }
                    return self::qCloudTemplate;
                },
                'data' => [
                    'code' => $verificationCode,
                ]
            ]);
        } catch (Exception $exception) {
            Log::channel('third')->error('Sms Error: ' . $exception->getMessage());
            throw new RenderException(Code::SMS_ERROR, 'Sms send error');
        }
    }

    /**
     * @param string $phone
     * @param string $verificationCode
     * @throws RenderException
     */
    public function checkVerificationCode(string $phone, string $verificationCode)
    {
        $cacheKey = CacheHeader::Phone . $phone;
        // 缓存比对
        $verifyData = Cache::get($cacheKey);
        if (!$verifyData) {
            throw new RenderException(Code::INVALID_SMS_CODE, 'Sms Code Invalid');
        }
        if ($verifyData['code'] != $verificationCode) {
            throw new RenderException(Code::SMS_CODE_VERIFY_FAIL, 'Sms Code Verify Fail');
        }
        // 删除缓存
        Cache::forget($cacheKey);
    }
}