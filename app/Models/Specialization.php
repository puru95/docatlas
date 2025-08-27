<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $fillable = [
        'image_id',
        'department_id',
        'specialization_name',
        'specialization_code',
        'info',
        'status',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
