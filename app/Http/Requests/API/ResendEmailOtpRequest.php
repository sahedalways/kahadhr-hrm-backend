<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ResendEmailOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_email' => 'required|string',
            'company_name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'company_email.required' => 'Email is required.',
            'company_email.email' => 'Enter a valid email address.',
        ];
    }
}
