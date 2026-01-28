<?php

namespace App\Observers;

use App\Models\EmployeeProfile;

class EmployeeProfileObserver
{
    public function updating(EmployeeProfile $profile)
    {
        $addressFields = [
            'house_no',
            'street',
            'city',
            'state',
            'address',
            'postcode',
            'country',
        ];


        $addressChanged = collect($addressFields)
            ->contains(fn($field) => $profile->isDirty($field));

        if (! $addressChanged) {
            return;
        }


        $oldAddress = [];
        foreach ($addressFields as $field) {
            $oldAddress[$field] = $profile->getOriginal($field);
        }


        $profile->addressHistory()->updateOrCreate(
            [
                'employee_profile_id' => $profile->id,
            ],
            $oldAddress
        );
    }
}
