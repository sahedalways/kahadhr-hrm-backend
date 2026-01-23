<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaySlipRequest extends Model
{
    use FilterByUserType;

    protected $fillable = [
        'company_id',
        'user_id',
        'period',
        'status',
        'payslip_id',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payslip()
    {
        return $this->belongsTo(PaySlip::class, 'payslip_id');
    }
}
