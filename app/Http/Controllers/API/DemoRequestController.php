<?php

namespace App\Http\Controllers\API;

use App\Jobs\SendDemoRequestJob;
use Illuminate\Http\Request;
use App\Models\DemoRequest;
use App\Mail\DemoRequestMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DemoRequestController extends BaseController
{
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'company_name' => 'required|string|max:255',
            'employee_count' => 'required|string|max:50',
            'demo_date' => 'required|date|after_or_equal:today',
            'demo_time' => 'required|string|max:20',
            'recaptcha_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        $recaptchaVerified = $this->verifyRecaptcha($request->recaptcha_token);

        if (!$recaptchaVerified) {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification failed. Please try again.'
            ], 400);
        }

        try {
            $demoData = [
                'full_name' => $request->full_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'company_name' => $request->company_name,
                'employee_count' => $request->employee_count,
                'demo_date' => $request->demo_date,
                'demo_time' => $request->demo_time,
                'source' => $request->source ?? 'website',
                'request_id' => 'DEMO-' . strtoupper(uniqid()),
                'submitted_at' => now()->format('Y-m-d H:i:s')
            ];

            $emailSent = $this->sendNotificationEmails($demoData);

            if (!$emailSent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email. Please try again.'
                ], 500);
            }



            return response()->json([
                'success' => true,
                'message' => 'Demo request submitted successfully! We\'ll contact you soon.',
                'data' => [
                    'reference' => $demoData['request_id'],
                    'submitted_at' => $demoData['submitted_at']
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to create demo request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit demo request. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify reCAPTCHA token with Google
     */
    private function verifyRecaptcha($token)
    {
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $token
            ]);

            $body = $response->json();


            if (isset($body['success']) && $body['success'] === true) {
                return true;
            }

            Log::warning('reCAPTCHA verification failed', $body);
            return false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification emails
     */
    private function sendNotificationEmails($demoRequest)
    {
        try {
            SendDemoRequestJob::dispatch($demoRequest);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send demo request email: ' . $e->getMessage());
            return false;
        }
    }
}
