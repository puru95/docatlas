<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentService extends Model
{
    protected $fillable = [
        'department_id', 'service_name', 'service_cost', 'slot_duration_minutes', 
        'image_id', 'status', 'info', 'service_type',
    ];

    public function servicedaySchedules()
    {
        return $this->hasMany(ServiceDaySchedule::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
