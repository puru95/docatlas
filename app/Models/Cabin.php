<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabin extends Model
{
    protected $fillable = ['branch_id', 'room_id', 'cabin_name', 'cabin_number'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
