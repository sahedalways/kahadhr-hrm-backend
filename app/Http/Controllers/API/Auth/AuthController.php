<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\MatchPincodeRequest;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\RegisterEmailConfirmOTPRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Models\User;
use App\Services\API\EmailVerificationService;
use App\Services\API\ForgotPasswordService;
use App\Services\API\FrontAuthService;
use Illuminate\Http\Request;



class AuthController extends BaseController
{
    protected FrontAuthService $authService;
    protected ForgotPasswordService $forgotPasswordService;
    protected EmailVerificationService $emailVerificationService;


    public function __construct(ForgotPasswordService $ForgotPasswordService, FrontAuthService $authService, EmailVerificationService $emailVerificationService)
    {
        $this->forgotPasswordService = $ForgotPasswordService;
        $this->authService = $authService;
        $this->emailVerificationService = $emailVerificationService;
    }

    public function register(RegisterUserRequest $request)
    {
        $request->validated();

        // ✅ Call Service Layer
        $user = $this->authService->registerUserByAPI($request->only([
            'f_name',
            'l_name',
            'email',
            'phone_no',
            'password'
        ]));

        $name = $user->f_name . ' ' . $user->l_name;

        $sent = $this->emailVerificationService->sendOtp($user->email, $name);

        if (!$sent) {
            $user->delete();
            return $this->sendError('Unable to send verification email. Please try again later.');
        }

        return $this->sendResponse([
            'email'     => $user->email,
        ], 'User registered successfully.');
    }


    public function resendOtp(ResendOtpRequest $request)
    {
        // Validate request
        $validated = $request->validated();

        $email = $validated['email'];


        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }


        $name = $user->f_name . ' ' . $user->l_name;


        // Send OTP
        try {
            $sent = $this->emailVerificationService->sendOtp($user->email, $name);

            if (!$sent) {
                return $this->sendError('Failed to resend OTP. Please try again later.', [], 500);
            }

            return $this->sendResponse(
                'OTP has been resent successfully.',
                ['email' => $user->email]
            );
        } catch (\Exception $e) {
            return $this->sendError('Unable to send OTP. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }




    public function verifyEmailConfirmOTP(RegisterEmailConfirmOTPRequest $request)
    {
        try {
            $request->validated();

            $user = $this->emailVerificationService->verifyOtp($request->email, $request->otp);

            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $profile = $user->profile;

            return $this->sendResponse([
                'token'     => $token,
                'id'        => $user->id,
                'f_name'    => $user->f_name,
                'l_name'    => $user->l_name,
                'email'     => $user->email,
                'phone_no'  => $user->phone_no,
                'user_type' => $user->user_type,
                'profile'   => $profile,
            ], 'Email verified successfully.');
        } catch (\Exception $e) {
            // Return error message to frontend
            return $this->sendError($e->getMessage(), [], 400);
        }
    }


    // for login function
    public function login(LoginRequest $request)
    {
        $request->validated();


        try {
            $user = $this->authService->loginByAPI(
                $request->input('email'),
                $request->input('password')
            );



            if (!$user) {
                return $this->sendError('Email or Password is incorrect!', ['error' => 'Unauthorized']);
            }


            $name = $user->f_name . ' ' . $user->l_name;

            if (is_null($user->email_verified_at)) {

                $this->emailVerificationService->sendOtp($user->email, $name);

                return $this->sendResponse(
                    ['email' => $user->email],
                    'Email not verified. OTP sent to your email.'
                );
            }


            $newToken = $user->createToken('Personal Access Token');

            $profile = $user->profile;


            return $this->sendResponse([
                'token'     => $newToken,
                'id'        => $user->id,
                'f_name'    => $user->f_name,
                'l_name'    => $user->l_name,
                'email'     => $user->email,
                'phone_no'  => $user->phone_no,
                'user_type' => $user->user_type,
                'profile'   => $profile,
            ], 'User login successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Unauthorized', ['error' => $e->getMessage()]);
        }
    }



    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $result = $this->forgotPasswordService->handle(
                $request->input('email'),
            );

            return $this->sendResponse($result, 'OTP sent successfully.');
        } catch (\Exception $e) {

            return $this->sendError('Failed to send OTP.', [
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function matchPincode(MatchPincodeRequest $request)
    {
        try {
            $result = $this->authService->matchPincode($request->otp);


            return $this->sendResponse($result, 'Your OTP is correct!');
        } catch (\Exception $e) {
            return $this->sendError('OTP does not match.', ['error' => $e->getMessage()]);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $success = $this->authService->updatePasswordByAPI(
                $request->identifier,
                $request->password
            );

            if ($success) {
                return $this->sendResponse($success, 'Password has been changed successfully!');
            }

            return $this->sendError('Password change failed. User not found!');
        } catch (\Exception $e) {
            return $this->sendError('Password change failed.', ['error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Successfully logged out.']);
    }
}
