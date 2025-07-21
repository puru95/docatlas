<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cin',
        'legal_facility_name',
        'cin_doc_id',
        'company_pan',
        'company_pan_doc_id',
        'license_number',
        'license_issued_year',
        'license_doc_id',
        'tax_id',
        'tax_doc_id',
        'address_line_1',
        'address_line_2',
        'pincode',
        'province',
        'city',
        'country',
    ];

    // Relationship: HospitalInfo belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
