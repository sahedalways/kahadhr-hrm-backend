<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'emailOrPhone' => 'required|string',
            'otp'          => 'required|string|size:6',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'emailOrPhone.required' => 'Email or phone number is required.',
            'otp.required'          => 'OTP is required.',
            'otp.size'              => 'OTP must be exactly 6 digits.',
        ];
    }
}
