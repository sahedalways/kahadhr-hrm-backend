<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class SendPhoneOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => 'required|string|unique:companies,company_name',
            'company_phone'        => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'company_phone.required'        => 'Phone number is required.',

        ];
    }
}
