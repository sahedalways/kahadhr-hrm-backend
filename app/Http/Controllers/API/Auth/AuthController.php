<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\RegisterUserRequest as APIRegisterUserRequest;
use App\Http\Requests\API\ResendEmailOtpRequest;
use App\Http\Requests\API\SendEmailOtpRequest;
use App\Http\Requests\API\SendPhoneOtpRequest;
use App\Http\Requests\API\VerifyOtpRequest;
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
        ]));


        $baseDomain = config('app.base_domain');

        $fullDomain = "https://company.{$baseDomain}";


        return $this->sendResponse([
            'company_name'     => $user->company->company_name,
            'subdomain'    => $fullDomain,
        ], 'Company registered successfully.');
    }



    public function sendEmailOtp(SendEmailOtpRequest $request)
    {
        // validated data
        $data = $request->validated();


        $email = $data['company_email'];
        $companyName = $data['company_name'];

        // call service
        $sent = $this->verificationService->sendEmailOtp($email, $companyName);

        if ($sent) {
            return $this->sendResponse([], 'OTP sent successfully.');
        }

        return $this->sendError('Unable to send OTP, please try again.');
    }


    public function resendEmailOtp(ResendEmailOtpRequest $request)
    {
        // validated data
        $data = $request->validated();


        $email = $data['company_email'];
        $companyName = $data['company_name'];


        // call service
        $sent = $this->verificationService->sendEmailOtp($email, $companyName);

        if ($sent) {
            return $this->sendResponse([], 'OTP resent successfully.');
        }

        return $this->sendError('Unable to resend OTP, please try again.');
    }





    public function sendPhoneOtp(SendPhoneOtpRequest $request)
    {
        // validated data
        $data = $request->validated();


        $phoneNo = $data['company_phone'];
        $companyName = $data['company_name'];

        // call service
        $sent = $this->verificationService->sendPhoneOtp($phoneNo, $companyName);

        if ($sent) {
            return $this->sendResponse([], 'OTP sent successfully.');
        }

        return $this->sendError('Unable to send OTP, please try again.');
    }



    public function verifyOtp(VerifyOtpRequest $request)
    {
        // validated data
        $data = $request->validated();

        // email or phone
        $otp = $data['otp'];
        $emailOrPhone = $data['emailOrPhone'];


        try {
            // call service
            $sent = $this->verificationService->verifyOtp($emailOrPhone, $otp);


            return $this->sendResponse($sent, 'OTP verified successfully!');
        } catch (\Exception $e) {
            return $this->sendError('OTP does not match.', ['error' => $e->getMessage()]);
        }
    }
}
