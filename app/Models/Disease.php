<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = ['name', 'category', 'description', 'baseline_prevalence'];

    protected $table = 'disease_clinical_data';     // id = disease_id here
    protected $primaryKey = 'disease_id';

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class, 'disease_symptom')
                    ->withPivot('weight');
    }

    public function symptomLinks()   // pivot rows with weights
    {
        return $this->hasMany(DiseaseSymptom::class, 'disease_id');
    }

    /** Total possible weight for this disease (cached via withSum) */
    public function getTotalWeightAttribute()
    {
        return $this->symptomLinks->sum('weight');
    }
}
