<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DiseaseSymptom;
use App\Models\TreatmentPlan;
use App\Models\User;
use App\Services\DiagnosisService;
use App\Services\OpenAIAssistantService;
use App\Services\TreatmentDiagnosisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiagnosisController extends BaseController
{
    use ApiResponse;

    public function getMedicinesData(Request $request)
    {

        try {

            $query = $request->search_text;

            if (!$query) {
                return $this->error('Search query is required.', 400);
            }

            $data = DB::table('medicines')->select('id', 'name')
                ->where('name', 'like', "%$query%")
                ->orWhere('salt', 'like', "%$query%")
                ->orWhere('category', 'like', "%$query%")
                ->get();

            $responseData = [
                "total_count" => count($data),
                "search_response" => $data
            ];

            return $this->success($responseData, 'Users fetched successfully');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function getSymptomsData(Request $request)
    {

        try {

            $query = $request->search_text;

            if (!$query) {
                return $this->error('Search query is required.', 400);
            }

            $data = DB::table('symptoms')
                ->select('id', 'name')
                ->where('name', 'like', "%$query%")
                ->get();

            $responseData = [
                "total_count" => count($data),
                "search_response" => $data
            ];

            return $this->success($responseData, 'Users fetched successfully');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function getUserProfile($userId)
    {

        try {

            $data = User::select('id', 'name', 'email', 'mobileNumber')->find($userId);
            return $this->success($data, 'success');
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function rank(array $symptomIds): array
    {
        $rows = DiseaseSymptom::selectRaw('
                    disease_id,
                    SUM(weight)   AS matched_score,
                    COUNT(*)      AS matched_count
                ')
            ->whereIn('symptom_id', $symptomIds)
            ->groupBy('disease_id')
            ->with('disease:id,disease_name')
            ->get();

        $high = $medium = $low = [];

        foreach ($rows as $row) {
            $possible = DiseaseSymptom::where('disease_id', $row->disease_id)->sum('weight');
            $ratio    = $row->matched_score / $possible;
            $score    = 0.7 * $ratio + 0.3 * (0 / 100);
            $bucket   = $score >= 0.6 ? 'high' : ($score >= 0.3 ? 'medium' : 'low');

            ${$bucket}[] = [
                'id'              => $row->disease_id,
                'name'            => $row->disease_name,
                'match_ratio'     => round($ratio, 2),
                'final_score'     => round($score, 2),
                'matched_symptoms' => $row->matched_count
            ];
        }
        return ['high' => $high, 'medium' => $medium, 'low' => $low];
    }


    public function getDiseaseBySymptoms(Request $request, DiagnosisService $svc, TreatmentDiagnosisService $tvc)
    {

        try {

            $validated = $request->validate([
                'symptoms' => 'required|array',
                'symptoms.*' => 'integer|exists:symptoms,id',
            ]);

            $symptoms = $request['symptoms'];

            // $buckets = $this->rank($symptoms);
            $buckets = $svc->rank($symptoms);
            // $buckets = $tvc->rank($symptoms);
            // return response()->json($buckets);
            // dd(count($buckets['medium']));
            $responseData = [
                // "total_count" => count($buckets['high'])+count($buckets['medium'])+count($buckets['low']),
                "search_response" => $buckets
            ];

            return $this->success($responseData, 'Users fetched successfully');

            // dd($symptoms);

            return $this->success($data, 'success');
        } catch (\Throwable $e) {
            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function getDiseaseDetails(Request $request)
    {

        try {

            $d_id = $request['disease_id'];

            $data = DB::table('diseases')->select('diseases.description', 'diseases.name as dName', 'disease_clinical_data.disease_id as id', 'disease_clinical_data.disease_name', 'disease_clinical_data.category', 'disease_clinical_data.symptoms', 'disease_clinical_data.lab_tests', 'disease_clinical_data.procedures', 'disease_clinical_data.medicines', 'disease_clinical_data.salts', 'disease_clinical_data.advice', 'disease_clinical_data.follow_up')->leftJoin('disease_clinical_data', 'disease_clinical_data.disease_id', '=', 'diseases.id')->where('diseases.id', $d_id)->get();

            $responseData = [
                "total_count" => count($data),
                "search_response" => $data
            ];

            return $this->success($responseData, 'Users fetched successfully');

            dd($data);
        } catch (\Throwable $e) {
            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function getQuestionsByOpenAI(Request $request, OpenAIAssistantService $ai)
    {

        $symptoms = $request['symptoms'];
        $userId = Auth::user()->id;

        if (count($symptoms) == 0) {
            return $this->error('No symptoms found.', 400);
        }

        $input = implode(',', $symptoms);
        $assistantId = 'asst_whe6Emf9E2LY2lUL6rNoHGT1'; // You create this in OpenAI dashboard
        $response = $ai->chat('I have ' . $input, $assistantId, null);

        $threadId = $response['thread_id'];
        $assistantMessage = collect($response['messages'])
            ->firstWhere('role', 'assistant')['content'][0]['text']['value'];

        // 1. Extract questions using regex
        preg_match_all('/\d+\.\s(.+?)\n((?:\s+- .+\n)+)/', $assistantMessage, $matches, PREG_SET_ORDER);

        $quesSet = [];

        foreach ($matches as $index => $match) {
            $questionText = trim($match[1]);
            $optionsRaw = explode("\n", trim($match[2]));

            $options = array_map(function ($option) {
                return trim(preg_replace('/^- /', '', $option));
            }, $optionsRaw);

            $quesSet[$index]['question'] = $questionText;
            $quesSet[$index]['sequence_no'] = $index + 1;
            $quesSet[$index]['options'] = $options;

            DB::table('user_interactions')->insert([
                'thread_id'       => $threadId,
                'user_id'         =>$userId,
                'sequence_no'     => $index + 1,
                'question'        => $questionText,
                'options'         => json_encode($options),
            ]);
        }

        // $myData = DB::table('user_interactions')->select('*')->where('user_id', 1)->where('thread_id', 'thread_oqenEwMDjb8UXkXw73hHHWTQ')->get();

        // foreach ($myData as $index => $value) {
        //     $quesSet[$index]['question'] = $value->question;
        //     $quesSet[$index]['sequence_no'] = $value->sequence_no;
        //     $quesSet[$index]['options'] = json_decode($value->options);
        // }

        // $result = [
        //     "total_questions" => count($myData),
        //     'user_id' => $userId,
        //     'thread_id' => 'thread_oqenEwMDjb8UXkXw73hHHWTQ',
        //     "ques_data" => $quesSet
        // ];

        $result = [
            "total_questions" => count($matches),
            'user_id' => $userId,
            'thread_id' => $threadId,
            "ques_data" => $quesSet
        ];

        TreatmentPlan::create([
            'user_id' => $userId,
            'thread_id' => $threadId,
            'symptoms' => $input,
            'ques_data' => json_encode($matches),
        ]);

        return $this->success($result, 'success');
    }

    public function submitDiagnosisAnswers(Request $request, OpenAIAssistantService $ai)
    {
        $request->validate([
            'answers' => 'required',
            'thread_id' => 'required',
        ]);

        $assistantId = 'asst_whe6Emf9E2LY2lUL6rNoHGT1'; // You create this in OpenAI dashboard
        $threadId = $request['thread_id'];
        $answers = $request['answers'];

        $answerString = "Here are my answers to your questions:\n";

        foreach ($answers as $number => $response) {
            $answerString .= "{$number}. {$response}\n";
        }

        // dd($answerString);

        $result = $ai->chat($answerString, $assistantId, $threadId);

        $assistantMessage = collect($result['messages'])->firstWhere('role', 'assistant')['content'][0]['text']['value'];
        // dd($assistantMessage);
        $userId = Auth::user()->id;

        TreatmentPlan::where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->update([
                'answers' => $answerString,
                'treatment_plan' => $assistantMessage
            ]);

        $response = [
            'user_id' => $userId,
            'thread_id' => $threadId,
            "treatment_plan" => $assistantMessage
        ];

        return $this->success($response, 'success');
    }
}
