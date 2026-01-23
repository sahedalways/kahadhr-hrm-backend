<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class CompanyBankInfo extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'stripe_payment_method_id',
    ];

    // Bank info belongs to a Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
