<div class="max-w-2xl mx-auto mt-10 p-6 bg-white shadow rounded">

    <!-- Step Indicators -->
    <div class="flex justify-between mb-6">
        <span class="{{ $step == 1 ? 'font-bold text-blue-600' : 'text-gray-500' }}">Company Info</span>
        <span class="{{ $step == 2 ? 'font-bold text-blue-600' : 'text-gray-500' }}">Email Verification</span>
        <span class="{{ $step == 3 ? 'font-bold text-blue-600' : 'text-gray-500' }}">Phone Verification</span>
        <span class="{{ $step == 4 ? 'font-bold text-blue-600' : 'text-gray-500' }}">Bank Info</span>
    </div>

    <!-- Step 1: Company Info -->
    @if ($step == 1)
        <div>
            <input type="text" wire:model="company_name" placeholder="Company Name" class="w-full p-2 border mb-2">
            <input type="text" wire:model="company_number" placeholder="Company Number"
                class="w-full p-2 border mb-2">
            <input type="text" wire:model="company_mobile" placeholder="Company Mobile"
                class="w-full p-2 border mb-2">
            <input type="email" wire:model="company_email" placeholder="Company Email" class="w-full p-2 border mb-2">
            <input type="password" wire:model="password" placeholder="Password" class="w-full p-2 border mb-2">
            <input type="password" wire:model="password_confirmation" placeholder="Confirm Password"
                class="w-full p-2 border mb-4">
            <button wire:click="nextStep" class="bg-blue-600 text-white px-4 py-2 rounded">Next</button>
        </div>
    @endif

    <!-- Step 2: Email OTP -->
    @if ($step == 2)
        <div>
            <p>An OTP has been sent to {{ $company_email }}</p>
            <input type="text" wire:model="email_otp" placeholder="Enter Email OTP" class="w-full p-2 border mb-4">
            <button wire:click="sendEmailOtp" class="bg-blue-600 text-white px-4 py-2 rounded">Verify Email</button>
            <button wire:click="previousStep" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Back</button>
        </div>
    @endif

    <!-- Step 3: Phone OTP -->
    @if ($step == 3)
        <div>
            <p>An OTP has been sent to {{ $company_mobile }}</p>
            <input type="text" wire:model="phone_otp" placeholder="Enter Phone OTP" class="w-full p-2 border mb-4">
            <button wire:click="sendPhoneOtp" class="bg-blue-600 text-white px-4 py-2 rounded">Verify Phone</button>
            <button wire:click="previousStep" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Back</button>
        </div>
    @endif

    <!-- Step 4: Bank Info -->
    @if ($step == 4)
        <div>
            <input type="text" wire:model="bank_name" placeholder="Bank Name" class="w-full p-2 border mb-2">
            <input type="text" wire:model="card_number" placeholder="Card Number" class="w-full p-2 border mb-2">
            <input type="text" wire:model="expiry_date" placeholder="Expiry Date (MM/YY)"
                class="w-full p-2 border mb-2">
            <input type="text" wire:model="cvv" placeholder="CVV" class="w-full p-2 border mb-4">
            <button wire:click="completeRegistration" class="bg-green-600 text-white px-4 py-2 rounded">Finish</button>
            <button wire:click="previousStep" class="bg-gray-500 text-white px-4 py-2 rounded ml-2">Back</button>
        </div>
    @endif

</div>
