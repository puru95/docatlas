<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GuestPatient extends Model
{
    use HasFactory;

    public $incrementing = false; // for UUID primary key
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'group_id',
        'hospital_id',
        'name',
        'sex',
        'age',
        'mobile_number',
        'country_code',
        'source_type',
        'source_id',
    ];

    protected $casts = [
        'age' => 'array',
    ];
}
