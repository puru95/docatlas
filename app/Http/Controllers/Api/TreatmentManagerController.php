<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuestPatient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TreatmentManagerController extends Controller
{
    public function fetchAppointments(Request $request)
    {
        $doctorId = $request->input('doctor_id');

        $dummyAppointments = [];

        for ($i = 0; $i < 10; $i++) {
            $appointmentId = Str::uuid()->toString();
            $patientId = "patient_" . md5($i . Str::uuid()->toString());
            $timestamp = now()->subMinutes($i * 30);
            $timestampInMilliseconds = $timestamp->timestamp * 1000;

            $timestampH = now()->subDays($i * 2);
            $timestampInMillisecondsH = $timestampH->timestamp * 1000;

            $dummyAppointments[] = [
                'doctor_name' => 'Dr. John Doe',
                'timestamp' => $timestampInMilliseconds,
                'duration' => '30',
                'doctorId' => $doctorId ?? Str::uuid()->toString(),
                'patient' => [
                    'id' => $patientId,
                    'name' => 'Patient ' . $i,
                    'age' => rand(20, 60),
                    'gender' => $i % 2 === 0 ? 'Male' : 'Female',
                ],
                'patient_image' => null,
                'appointment_id' => $appointmentId,
                'otpVerified' => 1,
                'vitalSignUpdated' => 1,
                'appointment_status' => $i % 2 === 0 ? 'APPOINTMENT_STARTED' : 'COMPLETED',
                'service_type' => $i % 2 === 0 ? 'Consultation' : 'Follow-up',
                'treatmentHistory' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'creation_timestamp' => now()->timestamp - 1000,
                        'last_updated_timestamp' => now()->timestamp,
                        'appointment_id' => $appointmentId,
                        'doctor_id' => $doctorId ?? Str::uuid()->toString(),
                        'patient_id' => $patientId,
                        'medical_record_id' => Str::uuid()->toString(),
                        'duration' => '30',
                        'doctor_name' => 'Dr. John Doe',
                        'timestamp' => $timestampInMillisecondsH
                    ]
                ]
            ];
        }

        return response()->json($dummyAppointments);
    }

    public function fetchMedicalHistory(Request $request)
    {
        $patientId = $request->input('patient_id');

        $dummyMedicalHistory = [];

        for ($i = 0; $i < 5; $i++) {
            $dummyMedicalHistory[] = [
                'id' => Str::uuid()->toString(),
                'patient_id' => $patientId,
                'answer1' => 'Hypertension',
                'answer2' => 'Diagnosed 2 years ago',
                'answer3' => 'On regular medication',
                'created_at' => now()->subDays($i)->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ];
        }

        return response()->json([
            'status' => true,
            'search_response' => $dummyMedicalHistory
        ]);
    }

    public function getMedicalRecord(Request $request)
    {
        $medicalRecordId = $request->query('medical-record-id');

        if (!$medicalRecordId) {
            return response()->json(['error' => 'medical-record-id is required'], 400);
        }

        $now = now()->timestamp;

        $dummyData = [
            'medical_record_id' => $medicalRecordId,
            'appointment_status' => 'APPOINTMENT_STARTED',
            'is_otp_verified' => true,

            'vital_signs' => [
                [
                    'id' => Str::uuid(),
                    'temperature' => '98.6',
                    'pulse' => '72',
                    'weight' => '68',
                    'height' => '170',
                    'bmi' => '23.5',
                    'respiration_rate' => '16',
                    'systolic_blood_pressure' => '120',
                    'diastolic_blood_pressure' => '80',
                    'oxygen_saturation' => '98',
                    'blood_glucose' => '90',
                    'medical_record_id' => $medicalRecordId,
                ]
            ],

            'clinical_notes' => [
                [
                    'id' => Str::uuid(),
                    'notes' => 'Patient recovering well, mild cough.',
                    'type' => 'FOLLOW_UP',
                    'medical_record_id' => $medicalRecordId,
                    'image_id' => Str::uuid(),
                    'document' => null
                ]
            ],

            'lab_orders' => [
                [
                    'id' => Str::uuid(),
                    'instructions' => 'Take test on empty stomach.',
                    'creation_timestamp' => $now - 3600,
                    'last_updated_timestamp' => $now,
                    'medical_record_id' => $medicalRecordId,
                    'test_name' => 'CBC',
                    'test_type' => 'Blood'
                ]
            ],

            'prescriptions' => [
                [
                    'id' => Str::uuid(),
                    'name' => 'Paracetamol',
                    'dosage_count' => '500',
                    'dosage_unit' => 'mg',
                    'frequency_interval' => '8',
                    'frequency_interval_text' => 'Every 8 hours',
                    'is_sos' => false,
                    'frequency_type' => 'DROPDOWN',
                    'duration' => 3,
                    'intake' => 'After food',
                    'medical_record_id' => $medicalRecordId
                ]
            ],

            'treatment_files' => [
                [
                    'id' => Str::uuid(),
                    'notes' => 'X-ray attached.',
                    'creation_timestamp' => $now - 7200,
                    'last_updated_timestamp' => $now,
                    'medical_record_id' => $medicalRecordId,
                    'image_ids' => [Str::uuid(), Str::uuid()]
                ]
            ],

            'medical_procedures' => [
                [
                    'id' => Str::uuid(),
                    'name' => 'Tooth Extraction',
                    'cost' => 2500,
                    'discount' => 500,
                    'description' => 'Wisdom tooth removal',
                    'creation_timestamp' => $now - 86400,
                    'last_updated_timestamp' => $now,
                    'medical_record_id' => $medicalRecordId,
                    'is_completed' => true
                ]
            ],

            'next_followup_date' => now()->addDays(7)->toDateString(),
            'instructions' => 'Take rest and continue meds for 3 days.',
            'radiology' => [],
        ];

        return response()->json($dummyData);
    }

    public function store(Request $request)
    {

        $groupId = $request->header('X-TREINT-GROUP-ID');
        $hospitalId = $request->header('X-TREINT-HOSPITAL-ID');

        if (!$groupId || !$hospitalId) {
            return response()->json([
                'message' => 'Group Id or Hospital Id not found',
                'status' => false
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'sex' => 'required|in:Male,Female,Other',
            'age.unit' => 'required|in:YEAR,MONTH',
            'age.value' => 'required|integer|min:0',
            'survey_name' => 'required|string', // validated but not stored unless needed
            // 'mobile_number' => 'required|string',
            'country_code' => 'required|string',
            'source_type' => 'required|string',
            'source_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 422);
        }

        $groupId = $request->header('X-TREINT-GROUP-ID');
        $hospitalId = $request->header('X-TREINT-HOSPITAL-ID');

        $guestInfo = GuestPatient::create([
            'name' => $request->survey_name,
            'sex' => $request->sex,
            'age' => [
                'unit' => $request->input('age.unit'),
                'value' => $request->input('age.value')
            ],
            'mobile_number' => '9999999999',
            'country_code' => $request->country_code,
            'source_type' => $request->source_type,
            'source_id' => $request->source_id,
            'group_id' => $groupId,
            'hospital_id' => $hospitalId,
        ]);

        return response()->json([
            'message' => 'Guest patient created successfully',
            'id' => $guestInfo->id,
        ], 201);
    }

    public function storee(Request $request)
    {

        $data = [
            'planData' => $request['planData'],
            'symptoms' => $request['symptoms'],
            'treatmentPlan' => $request['treatmentPlan']
        ];

        $id = $request['id'];

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        Storage::disk('public')->put("$id.json", $jsonData);

        return response()->json([
            'message' => 'Treatment Plan created successfully',
            'sucess' => true,
        ], 201);
    }

    public function getTreatmentData(Request $request)
    {
        // Path to JSON file in storage
        $id = $request['id'];
        $path = "$id.json";

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'Fisle not found'.$id], 404);
        }

        // Read file content
        $jsonContent = Storage::disk('public')->get($path);

        // Convert to PHP array
        $data = json_decode($jsonContent, true);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
