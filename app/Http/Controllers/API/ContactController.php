<?php

namespace App\Http\Controllers\API;


use App\Http\Requests\API\ContactRequest;
use App\Jobs\SendContactMessageJob;
use App\Models\Contact;

class ContactController extends BaseController
{

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        $recaptchaVerified = verifyRecaptcha($request->recaptcha_token);

        if (!$recaptchaVerified) {
            return response()->json([
                'success' => false,
                'message' => 'reCAPTCHA verification failed. Please try again.'
            ], 400);
        }


        // Save to database
        $contact = Contact::create($validated);

        if ($contact) {
            SendContactMessageJob::dispatch($contact);
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact message sent successfully!',
        ]);
    }
}
