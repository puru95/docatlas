<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $fillable = ['name'];
    protected $table = 'symptoms';
    protected $primaryKey = 'id';

    public function diseases()
    {
        return $this->belongsToMany(Disease::class, 'disease_symptom')
                    ->withPivot('weight');
    }
}
