<?php

namespace App\Http\Requests;

use App\Rules\Phone;

class SmsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 获取当前方法名
        $actionName = getCurrentMethodName();
        // 自定义规则
        $phone = new Phone();

        switch ($actionName) {
            case 'getValidationCode':
                return [
                    'sms_type' => 'required|in:0,1,2',
                    'phone' => ['required', $phone]
                ];
            case 'checkValidationCode':
                return [
                    'phone' => ['required', $phone],
                    'code' => 'required'
                ];
            default:
                return [];
        }

    }
}
