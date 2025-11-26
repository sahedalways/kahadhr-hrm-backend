<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DocumentType extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'name',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            $user = auth()->check() ? app('authUser') : null;

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
