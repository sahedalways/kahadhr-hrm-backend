<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Company extends Model
{

    protected $fillable = [
        'user_id',
        'company_name',
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
        'status'
    ];

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
        static::updated(function ($company) {
            if ($company->isDirty('company_name')) {

                $user = $company->user;
                if ($user) {
                    $user->f_name = $company->company_name;
                    $user->save();
                }
            }
        });

        static::deleted(function ($company) {
            if ($company->company_logo && Storage::disk('public')->exists($company->company_logo)) {
                Storage::disk('public')->delete($company->company_logo);
            }
        });
    }


    // Automatically store uploaded logo
    public function setCompanyLogoAttribute($value)
    {
        if ($value) {

            $filename = Str::random(10) . '.' . $value->getClientOriginalExtension();
            $this->attributes['company_logo'] = $value->storeAs('company/logo', $filename, 'public');
        }
    }

    // Accessor for logo URL
    public function getCompanyLogoUrlAttribute()
    {
        return $this->company_logo
            ? Storage::url($this->company_logo)
            : asset('assets/img/default-image.jpg');
    }
}
