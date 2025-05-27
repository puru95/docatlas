<?php

namespace App\Services;

use App\Models\Disease;
use App\Models\DiseaseSymptom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TreatmentDiagnosisService
{
    /**
     * @param  array<int>  $symptomIds   IDs selected by the user
     * @param  int         $limit        how many diseases to return
     */
    public function rank(array $symptomIds, int $limit = 10): Collection
    {
        if (empty($symptomIds)) {
            return collect();   // or throw ValidationException
        }

        // 1️⃣ weights actually triggered by the user
        $matched = DB::table('disease_symptoms')
            ->selectRaw('disease_id, SUM(weight) AS matched_weight')
            ->whereIn('symptom_id', $symptomIds)
            ->groupBy('disease_id');

        // 2️⃣ total possible weight for each disease
        $totals  = DB::table('disease_symptoms')
            ->selectRaw('disease_id, SUM(weight) AS total_weight')
            ->groupBy('disease_id');

        // 3️⃣ probability sub-query (only aggregated cols)
        $probSub = DB::query()
            ->fromSub($matched, 'm')
            ->joinSub($totals, 't', 't.disease_id', '=', 'm.disease_id')
            ->selectRaw('m.disease_id,
                 m.matched_weight,
                 t.total_weight,
                 ROUND(100 * m.matched_weight / t.total_weight, 2) AS probability');

        // 4️⃣ join back to disease table, no extra GROUP BY needed
        return Disease::query()
            ->joinSub($probSub, 'p', 'p.disease_id', '=', 'disease_clinical_data.disease_id')
            ->orderByDesc('p.probability')
            ->orderByDesc('p.matched_weight')
            ->limit($limit)
            ->get([
                'disease_clinical_data.*',
                'p.matched_weight',
                'p.total_weight',
                'p.probability'
            ]);
    }
}
