<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'company_name' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'company_name.required' => 'Company name is required.',
            'company_name.string' => 'Company name must be a valid string.',
        ];
    }
}
