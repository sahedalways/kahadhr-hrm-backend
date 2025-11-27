<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CalendarYearSetting extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'calendar_year',
    ];





    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    // Global Scope based on user type
    protected static function booted()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {
            if (!auth()->check()) {
                return;
            }

            $user = app('authUser');

            if ($user->user_type === 'superAdmin') {
                $builder->whereNull('company_id');
            } elseif ($user->user_type === 'company') {
                $builder->where('company_id', $user->company->id ?? 0);
            } elseif ($user->user_type === 'employee' || $user->user_type === 'teamLead') {
                $builder->whereHas('company.employees', function (Builder $query) use ($user) {
                    $query->where('id', $user->id);
                });
            }
        });
    }
}
