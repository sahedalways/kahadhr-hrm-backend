<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
        'billing_plan_id',
        'subscription_status',
        'subscription_start',
        'subscription_end',
        'payment_failed_count',
        'trial_ends_at',
        'payment_status',
        'status',

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
        return $this->hasOne(CompanyBankInfo::class);
    }


    // One Company → Many Employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }


    public function activeEmployees()
    {
        return $this->hasMany(Employee::class)
            ->where('is_active', 1)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($q) {
                $q->where('created_at', '<=', Carbon::now()->subDays(3));
            });
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
        static::created(function ($company) {
            $superAdminSettings = SiteSetting::query()
                ->withoutGlobalScope('filterByUserType')
                ->whereNull('company_id')
                ->first();

            $copyrightText = $superAdminSettings ? $superAdminSettings->copyright_text : '';

            $superAdminSetting = SiteSetting::whereNull('company_id')->first();

            SiteSetting::create([
                'company_id'        => $company->id,
                'site_title'        => $company->company_name,
                'logo'              =>  $superAdminSetting->logo ?? null,
                'favicon'           =>  $superAdminSetting->favicon ?? null,
                'site_phone_number' => $company->company_mobile ?? null,
                'site_email'        => $superAdminSetting->site_email ?? null,
                'copyright_text'    => $copyrightText,
            ]);



            $defaultTypes = [
                'Passport',
                'Driving License',
                'Right to Work',
            ];

            foreach ($defaultTypes as $type) {
                DocumentType::create([
                    'company_id' => $company->id,
                    'user_id'    => $company->user_id,
                    'name'       => $type,
                ]);
            }

            CalendarYearSetting::create([
                'company_id'    => $company->id,
                'calendar_year' => 'english',
            ]);



            $defaultBreaks = [
                [
                    'title'    => 'Rest Break',
                    'type'     => 'Paid',
                    'duration' => 0.10,
                ],
                [
                    'title'    => 'Meal',
                    'type'     => 'Unpaid',
                    'duration' => 0.45,
                ],
                [
                    'title'    => 'Meal Break',
                    'type'     => 'Unpaid',
                    'duration' => 0.30,
                ],
            ];

            foreach ($defaultBreaks as $break) {
                ShiftBreak::create([
                    'company_id' => $company->id,
                    'title'      => $break['title'],
                    'type'       => $break['type'],
                    'duration'   => $break['duration'],
                ]);
            }
        });

        static::deleted(function ($company) {
            if ($company->company_logo && Storage::disk('public')->exists($company->company_logo)) {
                Storage::disk('public')->delete($company->company_logo);
            }
        });

        static::updated(function ($company) {
            $companyDirty = $company->isDirty(['company_email', 'company_mobile']);

            $user = $company->user;

            if ($companyDirty && $user) {


                $user->update([
                    'email'    => $company->company_email,
                    'phone_no' => $company->company_mobile,
                ]);
            }

            if ($companyDirty) {

                $siteSettings = SiteSetting::where('company_id', $company->id)->first();

                if ($siteSettings) {
                    $siteSettings->update([
                        'site_title'        => $company->company_name,
                        'site_email'        => $company->company_email,
                        'site_phone_number' => $company->company_mobile,
                    ]);
                }
            }
        });
    }



    // Accessor for logo URL
    public function getCompanyLogoUrlAttribute()
    {
        return $this->company_logo
            ? asset('storage/' . $this->company_logo)
            : asset('assets/img/default-avatar.png');
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


    public function calendarYearSetting()
    {
        return $this->hasOne(CalendarYearSetting::class, 'company_id', 'id');
    }


    public function defaultCard()
    {
        return $this->bankInfos()->latest()->first();
    }


    public function hasValidCard()
    {
        return $this->bankInfos()
            && !empty($this->bankInfos->stripe_payment_method_id);
    }


    public function notify($type, array $data)
    {
        return Notification::create([
            'company_id' => $this->id,
            'user_id' => null,
            'type' => $type,
            'data' => json_encode($data),
        ]);
    }
}
