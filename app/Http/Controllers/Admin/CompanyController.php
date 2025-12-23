<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\ToastTrait;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class CompanyController extends Controller
{
    use ToastTrait;
    public function companyDetails($id, Request $request)
    {
        $details = Company::with(['bankInfos', 'employees', 'billingPlan', 'calendarYearSetting'])
            ->findOrFail($id);

        $stripeCard = null;
        $paymentMethodId = $details->bankInfos?->first()?->stripe_payment_method_id;

        if ($paymentMethodId) {
            $stripeCard = $this->fetchStripeCardInfo($paymentMethodId);
        }

        $invoicesQuery = $details->invoices()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $invoicesQuery->where('invoice_number', 'like', '%' . $request->search . '%');
        }
        if ($start = request('start_date')) {
            $invoicesQuery->whereDate('created_at', '>=', $start);
        }

        if ($end = request('end_date')) {
            $invoicesQuery->whereDate('created_at', '<=', $end);
        }
        $invoices = $invoicesQuery->get();

        return view('livewire.backend.admin.company-details', compact('details', 'stripeCard', 'invoices'));
    }



    public function changeCompanyPassword(Request $request, $id)
    {

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $company = Company::findOrFail($id);

        // Update password
        $company->user->password = bcrypt($request->password);
        $company->user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }



    protected function fetchStripeCardInfo($paymentMethodId)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId);

        $card = $paymentMethod->card;

        return [
            'brand' => $card->brand,
            'last4' => $card->last4,
            'exp_month' => $card->exp_month,
            'exp_year' => $card->exp_year,
            'holder_name' => $paymentMethod->billing_details->name,
        ];
    }
}
