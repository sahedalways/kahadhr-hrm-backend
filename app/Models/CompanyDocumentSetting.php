<?php

namespace App\Models;

use App\Traits\Scopes\FilterByUserType;
use Illuminate\Database\Eloquent\Model;

class CompanyDocumentSetting extends Model
{
    use FilterByUserType;
    protected $fillable = [
        'company_id',
        'doc_expiry_days',
        'notification_frequency',
        'notification_type',
    ];

    /**
     * Relation: Setting belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
