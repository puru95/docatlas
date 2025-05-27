<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DiagnosisService
{
    public function rank(array $symptomIds): array
    {

        // dd($symptomIds);
        // Step 1: Get all diseases linked to the input symptoms
        $rows = DB::table('disease_symptoms as ds')
            ->join('diseases as d', 'ds.disease_id', '=', 'd.id')
            ->select(
                'ds.disease_id',
                'd.name',
                'd.category',
                'd.description',
                DB::raw('SUM(ds.weight) as matched_score'),
                DB::raw('COUNT(ds.symptom_id) as matched_count')
            )
            ->whereIn('ds.symptom_id', $symptomIds)
            ->groupBy('ds.disease_id', 'd.name', 'd.category', 'd.description')
            ->get();

        $high = [];
        $medium = [];
        $low = [];

        foreach ($rows as $row) {
            // Step 2: Get total possible score for this disease
            $possible = DB::table('disease_symptoms')
                ->where('disease_id', $row->disease_id)
                ->sum('weight');

            if ($possible == 0) {
                continue;
            }

            // Step 3: Normalize and compute final score
            $match_ratio = $row->matched_score / $possible;
            $baseline_prevalence = DB::table('diseases')->where('id', $row->disease_id)->value('baseline_prevalence');
            $final_score = round(0.7 * $match_ratio + 0.3 * ($baseline_prevalence / 100), 2);
            $percentage = round($final_score * 100);

            // Step 4: Bucket into high / medium / low
            $data = [
                'disease_id' => $row->disease_id,
                'name' => $row->name,
                'category' => $row->category,
                'description' => $row->description,
                'match_ratio' => round($match_ratio, 2),
                'final_score' => $final_score,
                'matched_symptoms' => $row->matched_count,
                'percentage' => $percentage,
            ];

            if ($final_score >= 0.6) {
                $high[] = $data;
            } elseif ($final_score >= 0.3) {
                $medium[] = $data;
            } else {
                $low[] = $data;
            }
        }

        return [
            'high' => collect($high)->sortByDesc('final_score')->values(),
            'medium' => collect($medium)->sortByDesc('final_score')->values(),
            'low' => collect($low)->sortByDesc('final_score')->values(),
        ];
    }
}
