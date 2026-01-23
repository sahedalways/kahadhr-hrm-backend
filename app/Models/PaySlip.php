<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class PaySlip extends Model
{
    use FilterByUserType;

    protected $fillable = [
        'company_id',
        'user_id',
        'period',
        'file_path',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function request()
    {
        return $this->hasOne(PaySlipRequest::class, 'payslip_id');
    }
}
