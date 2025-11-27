<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use FilterByUserType;

    protected $fillable = [
        'company_id',
        'billing_period_start',
        'billing_period_end',
        'admin_fee',
        'employee_fee',
        'total_employees_billed',
        'subtotal',
        'vat',
        'total',
        'status',
        'pdf_path',
    ];



    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
