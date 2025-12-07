<?php

namespace App\Traits\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait FilterByUserType
{

    protected static function bootFilterByUserType()
    {
        static::addGlobalScope('filterByUserType', function (Builder $builder) {

            $user = auth()->check() ? app('authUser') : null;

            if (!$user) {
                return $builder->whereNull('company_id');
            }

            if ($user->user_type === 'superAdmin') {
                return;
            }

            if ($user->user_type === 'company') {
                return $builder->where('company_id', $user->company->id ?? 0);
            }

            if (in_array($user->user_type, ['employee', 'teamLead'])) {

                // SAFE: Avoids recursive relationship loading
                return $builder->where(
                    'company_id',
                    $user->employee->company_id ?? 0
                );
            }
        });
    }
}
