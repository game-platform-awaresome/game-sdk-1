<?php

namespace App\Http\Requests;

use App\Rules\Phone;
use App\Rules\NotExists;

class AccountRequest extends FormRequest
{

    public function rules()
    {
        // 获取当前方法名
        $actionName = getCurrentMethodName();
        // 自定义规则
        $notExists = new NotExists('accounts');
        $phone = new Phone();

        switch ($actionName) {
            case 'visitorLogin':
                return [
                    'uuid' => [$notExists],
                    'device' => 'required',
                ];
                break;
            case 'visitorBind':
                return [
                    'uuid' => 'required|exists:accounts',
                    'phone' => ['required', $notExists, $phone],
                    'password' => 'required|regex:/^[\Sa-zA-Z0-9]{6,20}$/',
                ];
                break;
            case 'accountRegister':
                return [
                    'phone' => ['required', $notExists, $phone],
                    'password' => 'required|regex:/^[\Sa-zA-Z0-9]{6,20}$/',
                    'device' => 'required',
                ];
                break;
            case 'accountLogin':
                return [
                    'phone' => ['required', 'exists:accounts', $phone],
                    'password' => 'required|regex:/^[\Sa-zA-Z0-9]{6,20}$/',
                    'device' => 'required',
                ];
                break;
            case 'tokenRefresh':
                return [
                    'open_id' => 'required|exists:accounts',
                    'device' => 'required',
                ];
                break;
            case 'findPassword':
                return [
                    'phone' => ['required', 'exists:accounts', $phone],
                    'password' => 'required|regex:/^[\Sa-zA-Z0-9]{6,20}$/',
                ];
                break;
            case 'bindIdentity':
                return [
                    'open_id' => 'required|string',
                    'id_number' => 'required|string',
                    'id_name' => 'required|string'
                ];
                break;
            case 'changeIdentity':
                return [
                    'open_id' => 'required|string',
                    'old_id_number' => 'required|string',
                    'old_id_name' => 'required|string',
                    'id_number' => 'required|string',
                    'id_name' => 'required|string'
                ];
            default:
                return [];
        }
    }
}
