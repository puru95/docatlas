<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeekSchedule extends Model
{
    protected $fillable = ['branch_id', 'entity_type', 'is_24_hours'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function daySchedules()
    {
        return $this->hasMany(DaySchedule::class);
    }
}
