<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailOtpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => 'required|string|unique:companies,company_name',
            'company_email'        => 'required|email|unique:companies,company_email',
            'company_mobile'        => 'required|string|unique:companies,company_mobile',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_email.required' => 'Company email is required.',
            'company_name.string'   => 'Company name must be a valid string.',
            'company_name.unique'   => 'Company name already exists.',

            'company_email.email'           => 'Please enter a valid email address.',
            'company_email.unique'          => 'Email already exists.',

            'company_mobile.required'        => 'Phone number is required.',
            'company_mobile.string'          => 'Phone number must be valid.',
            'company_mobile.unique'          => 'Phone number already exists.',
        ];
    }
}
