<?php

namespace App\Http\Requests;

use App\Exceptions\Code;
use App\Exceptions\RenderException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest as BaseRequest;
use Illuminate\Support\Facades\Log;

class FormRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @param Validator $validator
     * @throws RenderException
     */
    protected function failedValidation(Validator $validator)
    {
        // 需要特别抛出的错误码
        $failedRules = $validator->failed();
        $failedMessages = $validator->errors()->getMessages();
        foreach ($failedRules as $filed => $failedRule) {
            $message = $failedMessages[$filed][0];
            // 手机号码格式错误导致验证不通过
            if (key_exists('App\Rules\Phone', $failedRule)) {
                throw new RenderException(Code::PHONE_NUMBER_FORMAT_NOT_CORRECT, $message);
            }
            // 手机号码已存在导致验证不通过（用于绑定和注册）
            if (key_exists('App\Rules\NotExits', $failedRule)) {
                throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, $message);
            }
            // 手机号未注册导致验证不通过（用于登录和密码找回）
            if (key_exists("Exists", $failedRule) && $filed == 'phone') {
                throw new RenderException(Code::PHONE_NUMBER_UNREGISTERED, $message);
            }
            // 用户名不存在导致验证不通过
            if (key_exists("Exists", $failedRule) && $filed == 'account_name') {
                throw new RenderException(Code::ACCOUNT_LOGIN_WRONG_PASSWORD, $message);
            }
            // UUID不存在导致验证不通过
            if (key_exists("Exists", $failedRule) && $filed == 'uuid') {
                throw new RenderException(Code::INVALID_UUID, $message);
            }
            // OPEN_ID不存在导致验证不通过
            if (key_exists("Exists", $failedRule) && $filed == 'open_id') {
                throw new RenderException(Code::INVALID_ORDER_ID, $message);
            }
            // 密码格式错误导致验证不通过
            if (key_exists("Regex", $failedRule) && $filed == 'password') {
                throw new RenderException(Code::WRONG_PASSWORD_FORMAT, $message);
            }
        }

        // 普遍抛出的错误码
        throw new RenderException(Code::BAD_PARAMS, $validator->errors()->first());
    }
}
