<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Company Basic Info
            'company_name'        => 'required|string|max_length:255',
            'company_house_number' => 'required|string|max:255',
            'company_mobile'      => 'required|string|min:10|max:20',
            'company_email'       => 'required|email|unique:users,email',

            // Password
            'password'            => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',

            // Bank Info
            'bank_name'           => 'required|string|max:255',
            'card_number'         => 'required|digits_between:12,19',
            'expiry_date'         => 'required|date_format:m/y',
            'cvv'                 => 'required|digits_between:3,4',
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required'         => 'Company name is required.',
            'company_house_number.required' => 'House number is required.',
            'company_mobile.required'       => 'Company mobile number is required.',
            'company_email.required'        => 'Company email is required.',
            'company_email.unique'          => 'This email is already registered.',

            'password.required'             => 'Password is required.',
            'password.confirmed'            => 'Passwords do not match.',

            'bank_name.required'            => 'Bank name is required.',
            'card_number.required'          => 'Card number is required.',
            'expiry_date.required'          => 'Expiry date is required.',
            'cvv.required'                  => 'CVV is required.',
        ];
    }
}
