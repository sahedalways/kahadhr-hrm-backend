<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpAddressHistory extends Model
{
    protected $fillable = [
        'house_no',
        'address',
        'street',
        'city',
        'state',
        'postcode',
        'country',
    ];
}
