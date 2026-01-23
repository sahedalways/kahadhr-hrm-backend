<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'amount',
        'description',
        'invoice_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
