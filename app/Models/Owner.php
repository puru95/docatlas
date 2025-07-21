<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'owner_name',
        'owner_mobile_number',
        'owner_email',
        'owner_designation',
        'extra_data', // optional for dynamic fields
    ];

    protected $casts = [
        'extra_data' => 'array', // If using a json column for extra data
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
