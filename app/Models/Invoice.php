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
        'employee_fee',
        'total_employees_billed',
        'subtotal',
        'total',
        'vat',
        'invoice_number',
        'currency',
        'status',
        'pdf_path',
    ];


    protected $casts = [
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'employee_fee' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'vat' => 'decimal:2',
    ];


    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
