<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['legal_facility_name', 'phone', 'email'];

    public function details()
    {
        return $this->hasOne(CompanyDetail::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'entity');
    }

    public function kyc()
    {
        return $this->hasOne(KYC::class, 'entity_id');
    }
}
