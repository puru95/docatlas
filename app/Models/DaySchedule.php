<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaySchedule extends Model
{
    protected $fillable = ['week_schedule_id', 'day_of_week', 'day_off'];

    public function weekSchedule()
    {
        return $this->belongsTo(WeekSchedule::class);
    }

    public function slots()
    {
        return $this->hasMany(ScheduleSlot::class);
    }
}
