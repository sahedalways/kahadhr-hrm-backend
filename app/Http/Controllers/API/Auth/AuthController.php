<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\RegisterUserRequest as APIRegisterUserRequest;
use App\Http\Requests\SendOtpRequest;
use App\Services\API\VerificationService;
use App\Services\API\FrontAuthService;




class AuthController extends BaseController
{
    protected FrontAuthService $authService;
    protected VerificationService $verificationService;


    public function __construct(FrontAuthService $authService, VerificationService $verificationService)
    {
        $this->authService = $authService;
        $this->verificationService = $verificationService;
    }

    public function register(APIRegisterUserRequest $request)
    {
        $request->validated();

        // ✅ Call Service Layer
        $user = $this->authService->registerCompany($request->only([
            'company_name',
            'company_house_number',
            'company_mobile',
            'company_email',
            'password',

            'bank_name',
            'card_number',
            'expiry_date',
            'cvv',
        ]));


        return $this->sendResponse([
            'company_name'     => $user->company->company_name,
        ], 'Company registered successfully.');
    }



    public function sendOtp(SendOtpRequest $request)
    {
        // validated data
        $data = $request->validated();

        // email or phone
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $companyName = $data['company_name'] ?? null;

        // call service
        $sent = $this->verificationService->sendOtp($email, $phone, $companyName);

        if ($sent) {
            return $this->sendResponse([], 'OTP sent successfully.');
        }

        return $this->sendError('Unable to send OTP, please try again.');
    }
}
