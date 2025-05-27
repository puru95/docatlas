<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseSymptom extends Model
{
    protected $table = 'disease_symptoms';
    public $timestamps = false;
    protected $fillable = ['disease_id', 'symptom_id', 'weight'];

    // 🚨 Add this
    // public function disease()
    // {
    //     return $this->belongsTo(Disease::class);
    // }

    public function disease()
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }

    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }
}
