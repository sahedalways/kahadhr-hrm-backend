<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'company_name'),
            ],
            'company_house_number' => 'required|string|max:255',
            'company_mobile' => [
                'required',
                'string',
                'min:10',
                'max:20',
                Rule::unique('companies', 'company_mobile'),
            ],
            'company_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('companies', 'company_email'),
            ],

            // Password
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',


        ];
    }

    public function messages(): array
    {
        return [
            // Company Messages
            'company_name.required'         => 'Company name is required.',
            'company_name.string'           => 'Company name must be a valid string.',
            'company_name.max'              => 'Company name cannot exceed 255 characters.',
            'company_name.unique'           => 'This company name is already registered.',

            'company_house_number.required' => 'House number is required.',
            'company_house_number.string'   => 'House number must be a valid string.',
            'company_house_number.max'      => 'House number cannot exceed 255 characters.',

            'company_mobile.required'       => 'Company mobile number is required.',
            'company_mobile.string'         => 'Company mobile number must be valid.',
            'company_mobile.min'            => 'Company mobile must be at least 10 digits.',
            'company_mobile.max'            => 'Company mobile cannot exceed 20 digits.',
            'company_mobile.unique'         => 'This mobile number is already registered.',

            'company_email.required'        => 'Company email is required.',
            'company_email.email'           => 'Please enter a valid email address.',
            'company_email.max'             => 'Company email cannot exceed 255 characters.',
            'company_email.unique'          => 'This email is already registered.',

            // Password Messages
            'password.required'             => 'Password is required.',
            'password.min'                  => 'Password must be at least 8 characters.',
            'password.confirmed'            => 'Passwords do not match.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.min'     => 'Password confirmation must be at least 8 characters.',


        ];
    }
}
