<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmailSetting extends Model
{
    protected $fillable = [
        'company_id',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }



    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            $user = app('authUser');

            // If no user, return superAdmin item
            if (!$user) {
                $builder->whereNull('company_id');
                return;
            }

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            } elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            } elseif (in_array($user->user_type, ['employee', 'teamLead'])) {
                $builder->whereHas('company.employees', function (Builder $query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        });
    }
}
