<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class CompanyBankInfo extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'bank_name',
        'card_number',
        'expiry_date',
        'cvv',
    ];

    // Bank info belongs to a Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
