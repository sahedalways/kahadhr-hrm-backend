<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SmsSetting extends Model
{

    protected $fillable = [
        'company_id',
        'provider',
        'twilio_sid',
        'twilio_auth_token',
        'twilio_from',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }




    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            $user = auth()->user();


            if (!$user) {
                $builder->whereNull('company_id');
                return;
            }

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            }
            // Company user sees items for their company
            elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            }
            // Employee or Team Lead sees items related to their company
            elseif (in_array($user->user_type, ['employee', 'teamLead'])) {
                $builder->whereHas('company.employees', function (Builder $query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        });
    }
}
