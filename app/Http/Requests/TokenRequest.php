<?php

namespace App\Http\Requests;

class TokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_id' => 'required',
            'token' => 'required|string'
        ];
    }
}
