<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleSlot extends Model
{
    protected $fillable = ['day_schedule_id', 'start_time_utc', 'end_time_utc'];

    public function daySchedule()
    {
        return $this->belongsTo(DaySchedule::class);
    }
}
