<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Company extends Model
{

    protected $fillable = [
        'user_id',
        'company_name',
        'sub_domain',
        'company_house_number',
        'company_mobile',
        'company_email',
        'business_type',
        'address_contact_info',
        'company_logo',
        'registered_domain',
        'calendar_year',
        'billing_plan_id',
        'subscription_status',
        'subscription_start',
        'subscription_end',
        'payment_failed_count',
        'payment_status',
        'status'
    ];


    public function getSubscriptionStartAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function getSubscriptionEndAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    // Company belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // One Company → Many Bank Infos
    public function bankInfos()
    {
        return $this->hasMany(CompanyBankInfo::class);
    }


    // One Company → Many Employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Company belongs to a billing plan
    public function billingPlan()
    {
        return $this->belongsTo(BillingPlan::class);
    }


    // Company Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }



    protected static function booted()
    {


        static::deleted(function ($company) {
            if ($company->company_logo && Storage::disk('public')->exists($company->company_logo)) {
                Storage::disk('public')->delete($company->company_logo);
            }
        });



        static::creating(function ($company) {
            if (empty($company->sub_domain) && !empty($company->company_name)) {

                $cleanName = preg_replace('/\b(ltd|limited|pvt|private|inc|co|company)\b/i', '', $company->company_name);
                $company->sub_domain = Str::slug($cleanName);
            }
        });

        static::updating(function ($company) {
            if ($company->isDirty('company_name')) {

                $cleanName = preg_replace('/\b(ltd|limited|pvt|private|inc|co|company)\b/i', '', $company->company_name);
                $company->sub_domain = Str::slug($cleanName);

                $user = $company->user;
                if ($user) {
                    $user->f_name = $company->company_name;
                    $user->save();
                }
            }
        });
    }



    // Accessor for logo URL
    public function getCompanyLogoUrlAttribute()
    {
        return $this->company_logo
            ? asset('storage/' . $this->company_logo)
            : asset('assets/img/default-image.jpg');
    }


    /**
     * Get per-employee charge rate for this company
     *
     * @return float
     */
    public function perEmployeeCharge(): float
    {
        $chargeRate = CompanyChargeRate::first();
        return $chargeRate ? $chargeRate->rate : 0;
    }

    /**
     * Calculate monthly amount for this company
     *
     * @return float
     */
    public function monthlyAmount(): float
    {
        $employeeCount = $this->employees()->count();
        return $employeeCount * $this->perEmployeeCharge();
    }
}
