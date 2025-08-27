<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDetail extends Model
{
    protected $fillable = [
        'company_id',
        'bank_account_holder',
        'bank_account_number',
        'bank_ifsc',
        'company_pan',
        'cin',
        'cin_doc_id',
        'company_pan_doc_id',
        'tax_doc_id',
        'tax_id',
        'license_number',
        'license_number_doc_id',
        'license_issued_year'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
