<?php

namespace App\Http\Requests;

class ClientErrorRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'api' => 'required|string',
            'error_no' => 'required|string',
            'error_msg' => 'required|string',
            'file' => 'required|string',
            'error_line' => 'required|string',
            'sdk_version' => 'required|string',
            'request_data' => 'required|string',
        ];
    }
}
