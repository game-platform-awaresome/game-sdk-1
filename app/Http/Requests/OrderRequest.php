<?php

namespace App\Http\Requests;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $actionName = getCurrentMethodName();
        switch ($actionName) {
            case 'store':
                return [
                    'open_id' => 'required|string',
                    'amount' => 'required|string',
                    'device' => 'required|string',
                    'item_id' => 'required|string',
                    'item_name' => 'required|string',
                    'role_id' => 'required|string',
                    'role_name' => 'required|string',
                    'server_id' => 'required|string',
                    'order_type' => 'required',
                    'cp_order_no' => 'required|string',
                    'extra_data' => 'string',
                    'channel_id' => 'string',
                    'sub_chan_merchant' => 'string',
                ];
                break;
            case 'unifiedOrder':
                return [
                    'order_id' => 'required|string',
                    'pay_way' => 'required|string|in:1,2',
                ];
                break;
            case 'update':
            case 'orderStatus':
                return [
                    'order_id' => 'required|string'
                ];
                break;
            default:
                return [];
        }
    }
}
