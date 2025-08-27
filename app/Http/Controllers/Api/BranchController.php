<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Branch;
use App\Models\Building;
use App\Models\Cabin;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Wing;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchController extends BaseController
{
    use ApiResponse;

    public function createBranch(Request $request)
    {
        DB::beginTransaction();

        try {
            // Create Branch
            $branch = Branch::create([
                'name' => $request->branch['name'],
                'image_id' => $request->branch['image_id'] ?? null
            ]);

            // Create Address
            $branch->address()->create($request->address);

            // Create Week Schedule
            $weekSchedule = $branch->weekSchedule()->create([
                'entity_type' => $request->week_schedule['entity_type'],
                'is_24_hours' => $request->week_schedule['is_24_hours']
            ]);

            foreach ($request->week_schedule['day_schedule'] as $day) {
                $daySchedule = $weekSchedule->daySchedules()->create([
                    'day_of_week' => $day['day_of_week'],
                    'day_off' => $day['day_off'],
                ]);

                foreach ($day['slots'] as $slot) {
                    $daySchedule->slots()->create([
                        'start_time_utc' => $slot['start_time_utc'],
                        'end_time_utc' => $slot['end_time_utc'],
                    ]);
                }
            }

            DB::commit();

            $branchData = Branch::with('address', 'weekSchedule.daySchedules.slots')->find($branch->id);

            return response()->json([
                'total_count'     => $branchData->count(),
                'message' => 'Branch created successfully',
                'search_response' => $branchData,
            ]);

            // return response()->json(['message' => 'Branch created successfully', 'data' => $branch->load('address', 'weekSchedule.daySchedules.slots')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request)
    {
        $startFrom = $request->input('pagination.start_from', 0);
        $pageSize  = $request->input('pagination.page_size', 100);
        $searchText = $request->input('search_text');

        // Build the query with relation
        $query = Branch::with('address');

        // Apply search filter if exists (partial match)
        if (!empty($searchText)) {
            $query->where('name', 'LIKE', '%' . $searchText . '%');
        }

        // Get total before pagination
        $total = $query->count();

        // Apply pagination
        $branches = $query
            ->skip($startFrom)
            ->take($pageSize)
            ->get();

        // Format response with dummy counts
        $searchResponse = $branches->map(function ($branch) {
            return [
                'branch' => $branch,
                'department_count' => 2,
                'specializations_count' => 3,
                'services_count' => 4,
            ];
        });

        return response()->json([
            'total_count'     => $total,
            'search_response' => $searchResponse,
        ]);
    }

    public function listBranches(Request $request)
    {
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy total count
        $totalCount = 250;

        // Generate dummy data for example
        $branches = [];

        // Limit data generation to requested page_size
        for ($i = $startFrom; $i < min($startFrom + $pageSize, $totalCount); $i++) {
            $branches[] = [
                'id' => (string) Str::uuid(),
                'name' => "Branch " . ($i + 1),
            ];
        }

        return response()->json([
            'total_count' => $totalCount,
            'search_response' => $branches,
        ]);
    }

    public function getDepartmentList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy data
        $dummyData = [
            [
                "image" => [
                    "id" => "img1",
                    "image_name" => "image1.jpg",
                    "original_image_id" => "orig1",
                    "content_type" => "image/jpeg",
                    "user_id" => "u1",
                    "image_storage_url" => "https://example.com/images/image1.jpg",
                    "image_dir_key" => "dir1",
                    "image_file_name" => "image1.jpg"
                ],
                "department" => [
                    "id" => "d1",
                    "name" => "Cardiology",
                    "code" => "CARD",
                    "info" => "Heart related department",
                    "logo_img" => "logo1.jpg",
                    "image_id" => "img1",
                    "company_id" => "c1",
                    "group_id" => "g1",
                    "hospital_id" => "h1",
                    "branch_id" => "b1",
                    "details_id" => "det1",
                    "social_details_id" => "soc1",
                    "category" => "clinical",
                    "status" => "ACTIVE"
                ],
                "details" => [
                    "description" => "Handles all heart-related conditions"
                ]
            ],
            [
                "image" => [
                    "id" => "img1",
                    "image_name" => "image1.jpg",
                    "original_image_id" => "orig1",
                    "content_type" => "image/jpeg",
                    "user_id" => "u1",
                    "image_storage_url" => "https://example.com/images/image1.jpg",
                    "image_dir_key" => "dir1",
                    "image_file_name" => "image1.jpg"
                ],
                "department" => [
                    "id" => "d1",
                    "name" => "Finance & Accounts",
                    "code" => "CARD",
                    "info" => "Heart related department",
                    "logo_img" => "logo1.jpg",
                    "image_id" => "img1",
                    "company_id" => "c1",
                    "group_id" => "g1",
                    "hospital_id" => "h1",
                    "branch_id" => "b1",
                    "details_id" => "det1",
                    "social_details_id" => "soc1",
                    "category" => "non_clinical",
                    "status" => "ACTIVE"
                ],
                "details" => [
                    "description" => "Handles all heart-related conditions"
                ]
            ]
            // Add 4 more dummy departments here...
        ];

        // Search filtering
        $filtered = array_filter($dummyData, function ($item) use ($searchText) {
            return $searchText === '' || stripos($item['department']['name'], $searchText) !== false;
        });

        $filtered = array_values($filtered); // reindex

        $paginated = array_slice($filtered, $startFrom, $pageSize);
        $totalCount = count($filtered);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);
    }

    public function getSpecializationList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $departmentId = $request->input('department_id');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy specializations data (5 rows)
        $dummyData = [
            [
                'specialization' => [
                    'id'                   => 'sp1',
                    'specialization_name'  => 'Cardiology',
                    'specialization_code'  => 'SP1',
                    'description'          => 'Heart Specialist',
                    'department_id'        => 'd1',
                    'status'               => 'ACTIVE',
                ],
                'department' => [
                    'id'   => 'd1',
                    'name' => 'Cardiology',
                ],
            ],
            [
                'specialization' => [
                    'id'                   => 'sp2',
                    'specialization_name'  => 'Neurology',
                    'specialization_code'  => 'SP2',
                    'description'          => 'Brain & Nerve Specialist',
                    'department_id'        => 'd2',
                    'status'               => 'ACTIVE',
                ],
                'department' => [
                    'id'   => 'd2',
                    'name' => 'Neurology',
                ],
            ],
            [
                'specialization' => [
                    'id'                   => 'sp3',
                    'specialization_name'  => 'Orthopedics',
                    'specialization_code'  => 'SP3',
                    'description'          => 'Bone & Joint Specialist',
                    'department_id'        => 'd3',
                    'status'               => 'ACTIVE',
                ],
                'department' => [
                    'id'   => 'd3',
                    'name' => 'Orthopedics',
                ],
            ],
            [
                'specialization' => [
                    'id'                   => 'sp4',
                    'specialization_name'  => 'Pediatrics',
                    'specialization_code'  => 'SP4',
                    'description'          => 'Child Health Specialist',
                    'department_id'        => 'd4',
                    'status'               => 'ACTIVE',
                ],
                'department' => [
                    'id'   => 'd4',
                    'name' => 'Pediatrics',
                ],
            ],
            [
                'specialization' => [
                    'id'                   => 'sp5',
                    'specialization_name'  => 'Dermatology',
                    'specialization_code'  => 'SP5',
                    'description'          => 'Skin Specialist',
                    'department_id'        => 'd5',
                    'status'               => 'ACTIVE',
                ],
                'department' => [
                    'id'   => 'd5',
                    'name' => 'Dermatology',
                ],
            ],
        ];


        // Filter by department_id
        if ($departmentId)
            $filtered = array_filter($dummyData, function ($item) use ($departmentId) {
                return $item['specialization']['department_id'] === $departmentId;
            });

        // Filter by search_text (optional)
        if ($searchText)
            $filtered = array_filter(isset($filtered) ? $filtered : $dummyData, function ($item) use ($searchText) {
                return $searchText === '' || stripos($item['specialization']['name'], $searchText) !== false;
            });

        // Reset indexes
        $result = isset($filtered) ? array_values($filtered) : $dummyData;

        // Pagination
        $paginated = array_slice($result, $startFrom, $pageSize);
        $totalCount = count($result);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);
    }

    public function getServiceList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $branchId = $request->input('branch_id');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy specializations data (5 rows)
        $dummyData = [
            [
                'department_service' => [
                    'id' => 'srv1',
                    'service_name' => 'ECG',
                    'service_code' => 'SRV001',
                    'description' => 'Electrocardiogram test for heart health',
                    'department_id' => 'd1',
                    'branch_id' => '1',
                    'status' => 'ACTIVE',
                    'service_cost' => '200'
                ],
                'department' => [
                    'id' => 'd1',
                    'name' => 'Cardiology',
                ],
                'branch' => [
                    'id' => '1',
                    'name' => 'Main Branch',
                ]
            ],
            [
                'department_service' => [
                    'id' => 'srv2',
                    'service_name' => 'MRI Brain',
                    'service_code' => 'SRV002',
                    'description' => 'Magnetic Resonance Imaging of the brain',
                    'department_id' => 'd2',
                    'branch_id' => '1',
                    'status' => 'ACTIVE',
                    'service_cost' => '200'
                ],
                'department' => [
                    'id' => 'd2',
                    'name' => 'Neurology',
                ],
                'branch' => [
                    'id' => '1',
                    'name' => 'Main Branch',
                ]
            ],
            [
                'department_service' => [
                    'id' => 'srv3',
                    'service_name' => 'X-Ray Chest',
                    'service_code' => 'SRV003',
                    'description' => 'X-Ray for chest and lungs',
                    'department_id' => 'd3',
                    'branch_id' => '1',
                    'status' => 'ACTIVE',
                    'service_cost' => '200'
                ],
                'department' => [
                    'id' => 'd3',
                    'name' => 'Radiology',
                ],
                'branch' => [
                    'id' => '1',
                    'name' => 'Satellite Branch',
                ]
            ],
            [
                'department_service' => [
                    'id' => 'srv4',
                    'service_name' => 'Pediatric Consultation',
                    'service_code' => 'SRV004',
                    'description' => 'General checkup for children',
                    'department_id' => 'd4',
                    'branch_id' => '1',
                    'status' => 'ACTIVE',
                    'service_cost' => '200'
                ],
                'department' => [
                    'id' => 'd4',
                    'name' => 'Pediatrics',
                ],
                'branch' => [
                    'id' => '1',
                    'name' => 'Main Branch',
                ]
            ],
            [
                'department_service' => [
                    'id' => 'srv5',
                    'service_name' => 'Skin Allergy Test',
                    'service_code' => 'SRV005',
                    'description' => 'Testing for various skin allergies',
                    'department_id' => 'd5',
                    'branch_id' => '1',
                    'status' => 'ACTIVE',
                    'service_cost' => '200'
                ],
                'department' => [
                    'id' => 'd5',
                    'name' => 'Dermatology',
                ],
                'branch' => [
                    'id' => '1',
                    'name' => 'Clinic Branch',
                ]
            ]
        ];



        // Filter by department_id
        if ($branchId)
            $filtered = array_filter($dummyData, function ($item) use ($branchId) {
                return $item['branch']['id'] == $branchId;
            });

        // Filter by search_text (optional)
        if ($searchText)
            $filtered = array_filter(isset($filtered) ? $filtered : $dummyData, function ($item) use ($searchText) {
                return $searchText === '' || stripos($item['department_service']['service_name'], $searchText) !== false;
            });

        // Reset indexes
        $result = isset($filtered) ? array_values($filtered) : $dummyData;

        // Pagination
        $paginated = array_slice($result, $startFrom, $pageSize);
        $totalCount = count($result);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);
    }

    public function getEmployeeList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $employeeType = $request->input('employee_type', '');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy employee data
        $dummyData = [
            [
                'employee' => [
                    'id' => 'emp1',
                    'emp_type' => 'DOCTOR',
                    'email_id' => 'ayesha.khan@hospital.com',
                ],
                'user' => [
                    'full_txt_name' => 'Dr. Ayesha Khan',
                    'gender' => 'Female',
                    'dob' => '1985-07-12',
                    'mobile_no' => '9876543210',
                ],
                'departments' => [
                    ['id' => 'd1', 'name' => 'Cardiology'],
                    ['id' => 'd2', 'name' => 'General Medicine'],
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/images/emp1.jpg',
                ],
            ],
            [
                'employee' => [
                    'id' => 'emp2',
                    'emp_type' => 'MANAGER',
                    'email_id' => 'ravi.sharma@hospital.com',
                ],
                'user' => [
                    'full_txt_name' => 'Ravi Sharma',
                    'gender' => 'Male',
                    'dob' => '1990-02-20',
                    'mobile_no' => '9876500000',
                ],
                'departments' => [
                    ['id' => 'd3', 'name' => 'Operations'],
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/images/emp2.jpg',
                ],
            ],
            [
                'employee' => [
                    'id' => 'emp3',
                    'emp_type' => 'ADMIN',
                    'email_id' => 'admin.user@hospital.com',
                ],
                'user' => [
                    'full_txt_name' => 'Admin User',
                    'gender' => 'Other',
                    'dob' => '1980-01-01',
                    'mobile_no' => '',
                ],
                'departments' => [],
                'image' => null,
            ],
            [
                'employee' => [
                    'id' => 'emp4',
                    'emp_type' => 'NURSE',
                    'email_id' => 'anita.singh@hospital.com',
                ],
                'user' => [
                    'full_txt_name' => 'Anita Singh',
                    'gender' => 'Female',
                    'dob' => '1992-08-15',
                    'mobile_no' => '9988776655',
                ],
                'departments' => [
                    ['id' => 'd4', 'name' => 'Pediatrics'],
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/images/emp4.jpg',
                ],
            ],
        ];


        // Filter by department_id
        if ($employeeType)
            $filtered = array_filter($dummyData, function ($item) use ($employeeType) {
                return $employeeType === '' || $item['employee']['emp_type'] === $employeeType;
            });

        // Filter by search_text (optional)
        if ($searchText)
            $filtered = array_filter(isset($filtered) ? $filtered : $dummyData, function ($item) use ($searchText) {
                return $searchText === '' || stripos($item['user']['full_txt_name'], $searchText) !== false;
            });

        // Reset index
        $result = isset($filtered) ? array_values($filtered) : $dummyData;

        // Pagination
        $paginated = array_slice($result, $startFrom, $pageSize);
        $totalCount = count($result);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);
    }

    public function getPatientList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $pagination = $request->input('pagination', []);
        $startFrom = $pagination['start_from'] ?? 0;
        $pageSize = $pagination['page_size'] ?? 10;

        // Dummy employee data
        $dummyData = [
            [
                'patient' => [
                    'id' => 'pat1',
                    'first_name' => 'Rahul',
                    'last_name' => 'Verma',
                    'full_txt_name' => 'Rahul Verma',
                    'gender' => 'Male',
                    'dob' => '1990-05-15',
                    'status' => 'ACTIVE',
                    'mobile' => '9876543210',
                    'code' => 'PAT01',
                    'email' => 'rahual@gmail.com'
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/patient1.jpg',
                ],
                'address' => [
                    'city' => 'New Delhi'
                ]
            ],
            [
                'patient' => [
                    'id' => 'pat2',
                    'first_name' => 'Pooja',
                    'last_name' => 'Mishra',
                    'full_txt_name' => 'Pooja Mishra',
                    'gender' => 'Female',
                    'dob' => '1987-03-22',
                    'status' => 'ACTIVE',
                    'mobile' => '9823456789',
                    'code' => 'PAT02',
                    'email' => 'rahual@gmail.com'
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/patient2.jpg',
                ],
                'address' => [
                    'city' => 'New Delhi'
                ]
            ],
            [
                'patient' => [
                    'id' => 'pat3',
                    'first_name' => 'Aman',
                    'last_name' => 'Kumar',
                    'full_txt_name' => 'Aman Kumar',
                    'gender' => 'Male',
                    'dob' => '2001-11-10',
                    'status' => 'INACTIVE',
                    'mobile' => '9911223344',
                    'code' => 'PAT03',
                    'email' => 'rahual@gmail.com'
                ],
                'image' => null,
                'address' => [
                    'city' => 'New Delhi'
                ]
            ],
            [
                'patient' => [
                    'id' => 'pat4',
                    'first_name' => 'Neha',
                    'last_name' => 'Sharma',
                    'full_txt_name' => 'Neha Sharma',
                    'gender' => 'Female',
                    'dob' => '1995-07-18',
                    'status' => 'ACTIVE',
                    'mobile' => '9988776655',
                    'code' => 'PAT04',
                    'email' => 'rahual@gmail.com'
                ],
                'image' => [
                    'image_storage_url' => 'https://example.com/storage/patient4.jpg',
                ],
                'address' => [
                    'city' => 'New Delhi'
                ]
            ],
            [
                'patient' => [
                    'id' => 'pat5',
                    'first_name' => 'Farhan',
                    'last_name' => 'Ali',
                    'full_txt_name' => 'Farhan Ali',
                    'gender' => 'Male',
                    'dob' => '1982-12-01',
                    'status' => 'ACTIVE',
                    'mobile' => '9876001122',
                    'code' => 'PAT05',
                    'email' => 'rahual@gmail.com'
                ],
                'image' => null,
                'address' => [
                    'city' => 'New Delhi'
                ]
            ],
        ];

        // Filter by search_text (optional)
        if ($searchText)
            $filtered = array_filter($dummyData, function ($item) use ($searchText) {
                return $searchText === '' || stripos($item['patient']['full_txt_name'], $searchText) !== false;
            });

        // Reset index
        $result = isset($filtered) ? array_values($filtered) : $dummyData;

        // Pagination
        $paginated = array_slice($result, $startFrom, $pageSize);
        $totalCount = count($result);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);
    }

    public function getDoctorList(Request $request)
    {
        $searchText = $request->input('search_text', '');
        $startFrom = $request->input('pagination.start_from', 0);
        $pageSize = $request->input('pagination.page_size', 10);

        // Build query
        $dummyData = [
            [
                'doctor' => [
                    'id' => 'doc_001',
                    'creation_timestamp' => time() - 86400 * 100,
                    'last_updated_timestamp' => time(),
                    'group_id' => 'group_001',
                    'hospital_id' => 'hospital_001',
                    'branch_id' => 'branch_001',
                    'employee_id' => 'emp_001',
                    'user_id' => 'user_001',
                    'info' => 'Experienced Dermatologist',
                    'departments_list' => ['dep_001'],
                    'specialization_list' => ['spec_001', 'spec_002'],
                    'services_list' => ['srv_001', 'srv_002'],
                    'full_txt_name' => 'Dr. Aisha Verma',
                    'rating' => 4.8,
                    'review_count' => 124,
                    'status' => 'active',
                ],
                'employee' => [
                    'id' => 'emp_001',
                    'creation_timestamp' => time() - 86400 * 200,
                    'last_updated_timestamp' => time(),
                    'user_id' => 'user_001',
                    'emp_code' => 'EMP001',
                    'designation' => 'Senior Dermatologist',
                    'company_id' => 'comp_001',
                    'group_id' => 'group_001',
                    'hospital_id' => 'hospital_001',
                    'branch_id' => 'branch_001',
                    'details_id' => 'det_001',
                    'address_id' => 'addr_001',
                    'status' => 'active',
                    'emp_type' => 'full_time',
                    'emp_type_id' => 'et_001',
                    'departments_list' => ['dep_001'],
                    'doj' => strtotime('2015-04-10'),
                    'documents_list' => ['doc_1.pdf', 'doc_2.pdf'],
                    'salary' => 120000,
                    'qualifications_list' => ['MBBS', 'MD Dermatology'],
                    'total_exp_years' => 12,
                    'total_exp_months' => 3,
                    'identity_number1' => 'A1234567',
                    'identity_type1' => 'PAN',
                    'identity_number2' => 'X12345678',
                    'identity_type2' => 'Aadhaar',
                    'identity_type1_document_ids' => ['img_111'],
                    'identity_type2_document_ids' => ['img_112'],
                ],
                'user' => [
                    'id' => 'user_001',
                    'creation_timestamp' => time() - 86400 * 365,
                    'last_updated_timestamp' => time(),
                    'email_id' => 'aisha.verma@example.com',
                    'email_verified' => true,
                    'country_code' => '+91',
                    'mobile_no' => '9876543210',
                    'alternate_mobile_no' => '9876501234',
                    'gender' => 'female',
                    'dob' => strtotime('1985-06-15'),
                    'salutation' => 'Dr.',
                    'first_name' => 'Aisha',
                    'last_name' => 'Verma',
                    'full_txt_name' => 'Dr. Aisha Verma',
                    'personal_identity_type' => 'PAN',
                    'personal_identity_value' => 'A1234567',
                    'emergency_contact_name' => 'Rajeev Verma',
                    'emergency_contact_no' => '9876500000',
                    'registration_id' => 'reg_123456',
                    'profile_image' => 'https://example.com/images/aisha-verma.jpg',
                    'image_id' => 'img_001',
                    'status' => 'active',
                ],
                'departments' => [
                    [
                        'id' => 'dep_001',
                        'creation_timestamp' => time() - 86400 * 1000,
                        'last_updated_timestamp' => time(),
                        'name' => 'Dermatology',
                        'code' => 'DERM',
                        'info' => 'Skin, hair, and nail treatment',
                        'logo_img' => 'https://example.com/logos/derm.png',
                        'image_id' => 'img_dep_001',
                        'company_id' => 'comp_001',
                        'group_id' => 'group_001',
                        'hospital_id' => 'hospital_001',
                        'branch_id' => 'branch_001',
                        'details_id' => 'det_001',
                        'social_details_id' => 'soc_001',
                        'category' => 'medical',
                        'boost' => 10,
                        'tags_list' => ['skin', 'hair', 'nails'],
                        'status' => 'active',
                    ],
                ],
                'specializations' => [
                    [
                        'id' => 'spec_001',
                        'creation_timestamp' => time() - 86400 * 150,
                        'last_updated_timestamp' => time(),
                        'specialization_name' => 'Acne Treatment',
                        'specialization_tagline' => 'Expert in acne care',
                        'specialization_code' => 'ACNE',
                        'specialization_master_id' => 'sm_001',
                        'info' => 'Advanced acne treatments',
                        'logo_image_id' => 'img_201',
                        'image_id' => 'img_202',
                        'branch_id' => 'branch_001',
                        'department_id' => 'dep_001',
                        'details_id' => 'det_002',
                        'social_details_id' => 'soc_002',
                        'created_by_user' => 'admin_001',
                        'admin_list' => ['admin_001', 'admin_002'],
                        'boost' => 5,
                        'tags_list' => ['acne', 'face care'],
                        'status' => 'active',
                    ],
                ],
                'services' => [
                    [
                        'id' => 'srv_001',
                        'creation_timestamp' => time() - 86400 * 180,
                        'last_updated_timestamp' => time(),
                        'service_name' => 'Skin Consultation',
                        'service_tagline' => 'In-depth skin checkup',
                        'service_code' => 'SKINCONSULT',
                        'info' => 'Includes skin analysis and prescription',
                        'logo_image_id' => 'img_301',
                        'service_master_id' => 'sm_service_001',
                        'image_id' => 'img_302',
                        'hospital_id' => 'hospital_001',
                        'branch_id' => 'branch_001',
                        'department_id' => 'dep_001',
                        'slot_duration_minutes' => 30,
                        'service_cost' => 700,
                        'details_id' => 'det_003',
                        'social_details_id' => 'soc_003',
                        'boost' => 8,
                        'tags_list' => ['consultation', 'skin'],
                        'status' => 'active',
                        'service_type' => 'consultation',
                    ],
                ],
                'image' => [
                    'id' => 'img_001',
                    'creation_timestamp' => time() - 86400 * 365,
                    'last_updated_timestamp' => time(),
                    'image_name' => 'aisha-verma.jpg',
                    'original_image_id' => 'img_001_orig',
                    'image_type_id' => 'profile_pic',
                    'content_type' => 'image/jpeg',
                    'image_resolution' => '800x800',
                    'image_size' => 120000,
                    'user_id' => 'user_001',
                    'image_storage_url' => 'https://example.com/images/aisha-verma.jpg',
                    'image_dir_key' => 'profiles/2025/',
                    'image_file_name' => 'aisha-verma.jpg',
                ],
                'day_schedule' => [
                    'day_off' => false,
                    'day_of_week' => 'Monday',
                    'day_of_week_number' => 1,
                    'is_24_hours' => false,
                    'slots' => [
                        [
                            'schedule_range' => [
                                'gt' => strtotime('10:00'),
                                'lt' => strtotime('13:00')
                            ],
                            'start_time_utc' => '2025-07-15T04:30:00Z',
                            'end_time_utc' => '2025-07-15T07:30:00Z',
                        ],
                    ],
                ],
                'appointment_count' => 12,
            ],
            [
                'doctor' => [
                    'id' => 'doc_002',
                    'creation_timestamp' => time() - 86400 * 100,
                    'last_updated_timestamp' => time(),
                    'group_id' => 'group_001',
                    'hospital_id' => 'hospital_001',
                    'branch_id' => 'branch_001',
                    'employee_id' => 'emp_002',
                    'user_id' => 'user_001',
                    'info' => 'Experienced Dermatologist',
                    'departments_list' => ['dep_001'],
                    'specialization_list' => ['spec_001', 'spec_002'],
                    'services_list' => ['srv_001', 'srv_002'],
                    'full_txt_name' => 'Dr. Aisha Verma',
                    'rating' => 4.8,
                    'review_count' => 124,
                    'status' => 'active',
                ],
                'employee' => [
                    'id' => 'emp_002',
                    'creation_timestamp' => time() - 86400 * 200,
                    'last_updated_timestamp' => time(),
                    'user_id' => 'user_001',
                    'emp_code' => 'EMP002',
                    'designation' => 'Senior Dermatologist',
                    'company_id' => 'comp_001',
                    'group_id' => 'group_001',
                    'hospital_id' => 'hospital_001',
                    'branch_id' => 'branch_001',
                    'details_id' => 'det_001',
                    'address_id' => 'addr_001',
                    'status' => 'active',
                    'emp_type' => 'full_time',
                    'emp_type_id' => 'et_001',
                    'departments_list' => ['dep_001'],
                    'doj' => strtotime('2015-04-10'),
                    'documents_list' => ['doc_1.pdf', 'doc_2.pdf'],
                    'salary' => 120000,
                    'qualifications_list' => ['MBBS', 'MD Dermatology'],
                    'total_exp_years' => 12,
                    'total_exp_months' => 3,
                    'identity_number1' => 'A1234567',
                    'identity_type1' => 'PAN',
                    'identity_number2' => 'X12345678',
                    'identity_type2' => 'Aadhaar',
                    'identity_type1_document_ids' => ['img_111'],
                    'identity_type2_document_ids' => ['img_112'],
                ],
                'user' => [
                    'id' => 'user_002',
                    'creation_timestamp' => time() - 86400 * 365,
                    'last_updated_timestamp' => time(),
                    'email_id' => 'aisha.verma@example.com',
                    'email_verified' => true,
                    'country_code' => '+91',
                    'mobile_no' => '9876543210',
                    'alternate_mobile_no' => '9876501234',
                    'gender' => 'female',
                    'dob' => strtotime('1985-06-15'),
                    'salutation' => 'Dr.',
                    'first_name' => 'Aisha',
                    'last_name' => 'Verma',
                    'full_txt_name' => 'Dr. Steven Strange',
                    'personal_identity_type' => 'PAN',
                    'personal_identity_value' => 'A1234567',
                    'emergency_contact_name' => 'Wong',
                    'emergency_contact_no' => '9876500000',
                    'registration_id' => 'reg_123456',
                    'profile_image' => 'https://example.com/images/aisha-verma.jpg',
                    'image_id' => 'img_001',
                    'status' => 'active',
                ],
                'departments' => [
                    [
                        'id' => 'dep_001',
                        'creation_timestamp' => time() - 86400 * 1000,
                        'last_updated_timestamp' => time(),
                        'name' => 'Dermatology',
                        'code' => 'DERM',
                        'info' => 'Skin, hair, and nail treatment',
                        'logo_img' => 'https://example.com/logos/derm.png',
                        'image_id' => 'img_dep_001',
                        'company_id' => 'comp_001',
                        'group_id' => 'group_001',
                        'hospital_id' => 'hospital_001',
                        'branch_id' => 'branch_001',
                        'details_id' => 'det_001',
                        'social_details_id' => 'soc_001',
                        'category' => 'medical',
                        'boost' => 10,
                        'tags_list' => ['skin', 'hair', 'nails'],
                        'status' => 'active',
                    ],
                ],
                'specializations' => [
                    [
                        'id' => 'spec_001',
                        'creation_timestamp' => time() - 86400 * 150,
                        'last_updated_timestamp' => time(),
                        'specialization_name' => 'Acne Treatment',
                        'specialization_tagline' => 'Expert in acne care',
                        'specialization_code' => 'ACNE',
                        'specialization_master_id' => 'sm_001',
                        'info' => 'Advanced acne treatments',
                        'logo_image_id' => 'img_201',
                        'image_id' => 'img_202',
                        'branch_id' => 'branch_001',
                        'department_id' => 'dep_001',
                        'details_id' => 'det_002',
                        'social_details_id' => 'soc_002',
                        'created_by_user' => 'admin_001',
                        'admin_list' => ['admin_001', 'admin_002'],
                        'boost' => 5,
                        'tags_list' => ['acne', 'face care'],
                        'status' => 'active',
                    ],
                ],
                'services' => [
                    [
                        'id' => 'srv_001',
                        'creation_timestamp' => time() - 86400 * 180,
                        'last_updated_timestamp' => time(),
                        'service_name' => 'Skin Consultation',
                        'service_tagline' => 'In-depth skin checkup',
                        'service_code' => 'SKINCONSULT',
                        'info' => 'Includes skin analysis and prescription',
                        'logo_image_id' => 'img_301',
                        'service_master_id' => 'sm_service_001',
                        'image_id' => 'img_302',
                        'hospital_id' => 'hospital_001',
                        'branch_id' => 'branch_001',
                        'department_id' => 'dep_001',
                        'slot_duration_minutes' => 30,
                        'service_cost' => 700,
                        'details_id' => 'det_003',
                        'social_details_id' => 'soc_003',
                        'boost' => 8,
                        'tags_list' => ['consultation', 'skin'],
                        'status' => 'active',
                        'service_type' => 'consultation',
                    ],
                ],
                'image' => [
                    'id' => 'img_001',
                    'creation_timestamp' => time() - 86400 * 365,
                    'last_updated_timestamp' => time(),
                    'image_name' => 'aisha-verma.jpg',
                    'original_image_id' => 'img_001_orig',
                    'image_type_id' => 'profile_pic',
                    'content_type' => 'image/jpeg',
                    'image_resolution' => '800x800',
                    'image_size' => 120000,
                    'user_id' => 'user_001',
                    'image_storage_url' => 'https://example.com/images/aisha-verma.jpg',
                    'image_dir_key' => 'profiles/2025/',
                    'image_file_name' => 'aisha-verma.jpg',
                ],
                'day_schedule' => [
                    'day_off' => false,
                    'day_of_week' => 'Monday',
                    'day_of_week_number' => 1,
                    'is_24_hours' => false,
                    'slots' => [
                        [
                            'schedule_range' => [
                                'gt' => strtotime('10:00'),
                                'lt' => strtotime('13:00')
                            ],
                            'start_time_utc' => '2025-07-15T04:30:00Z',
                            'end_time_utc' => '2025-07-15T07:30:00Z',
                        ],
                    ],
                ],
                'appointment_count' => 12,
            ],
        ];

        // Filter by search_text (optional)
        if ($searchText)
            $filtered = array_filter($dummyData, function ($item) use ($searchText) {
                return $searchText === '' || stripos($item['doctor']['full_txt_name'], $searchText) !== false;
            });

        // Reset index
        $result = isset($filtered) ? array_values($filtered) : $dummyData;

        // Pagination
        $paginated = array_slice($result, $startFrom, $pageSize);
        $totalCount = count($result);

        return response()->json([
            'success' => true,
            'search_response' => $paginated,
            'total_count' => $totalCount
        ]);


        return response()->json([
            'search_response' => $searchResponse,
            'total_count' => $totalCount
        ]);
    }

    public function getDoctorSchedules(Request $request)
    {
        $filters = $request->only([
            'department_id',
            'specialization_id',
            'service_id',
            'doctor_id',
            'timestamp_range'
        ]);

        // Example: You can add logic here to filter data if you integrate with DB

        $dummyResponse = [
            'doctors_schedules' => [
                [
                    'schedules' => [
                        [
                            'id' => 'sched_001',
                            'creation_timestamp' => time() - 1000,
                            'last_updated_timestamp' => time(),
                            'entity_type' => 'doctor',
                            'entity_id' => $filters['doctor_id'] ?? 'doc_001',
                            'doctor_department_id' => $filters['department_id'] ?? 'dep_001',
                            'doctor_specialization_id' => $filters['specialization_id'] ?? 'spec_001',
                            'doctor_service_id' => $filters['service_id'] ?? 'srv_001',
                            'day_of_week' => 'Monday',
                            'day_of_week_number' => 1,
                            'start_time_utc' => '2025-07-16T16:20:35.123Z',
                            'end_time_utc' => '2025-07-16T16:20:35.123Z',
                            'schedule_range_millisOfDay' => [
                                'gt' => 16200000, // 4:30 AM
                                'lt' => 27000000  // 7:30 AM
                            ],
                            'start_time' => '2025-07-16T16:20:35.123Z',
                            'end_time' => '2025-07-16T18:20:35.123Z',
                            'start_time_epoch' => (string) strtotime('2025-07-16 10:00:00'),
                            'end_time_epoch' => (string) strtotime('2025-07-17 13:00:00')
                        ]
                    ],
                    'blockers' => [
                        [
                            'blocked_type' => 'appointment',
                            'blocked_type_id' => 'block_001',
                            'start_time_epoch' => strtotime('2025-07-16 10:30:00') * 1000,
                            'end_time_epoch' => strtotime('2025-07-16 11:30:00') * 1000,
                            'blocked_range' => [
                                'gt' => 37800000,
                                'lt' => 39600000
                            ],
                            'reason' => 'Patient Follow-up',
                            'patient_id' => 'pat_001',
                            'patient_name' => 'Rahul Sharma',
                            'patient_mobile' => '9876543210',
                            'patient_email' => 'rahul@example.com',
                            'patient_gender' => 'Male',
                            'patient_age' => '30',
                            'patient_blood_group' => 'B+',
                            'symptoms' => 'Rash and itching',
                            'appointment_status' => 'confirmed',
                            'image_id' => 'img_001',
                            'patient_code' => 'P0001',
                            'service_name' => 'Skin Consultation'
                        ]
                    ],
                    'holidays_leaves' => [
                        [
                            'id' => 'leave_001',
                            'entity_type' => 'doctor',
                            'entity_id' => $filters['doctor_id'] ?? 'doc_001',
                            'start_time' => '2025-07-18T00:00:00Z',
                            'end_time' => '2025-07-18T23:59:59Z',
                            'is_active' => true,
                            'start_time_epoch' => strtotime('2025-07-18 00:00:00'),
                            'end_time_epoch' => strtotime('2025-07-18 23:59:59')
                        ]
                    ],
                    'service_types' => 'CONSULTANT',
                    'doctor_id' => $filters['doctor_id'] ?? 'doc_001',
                    'doctor_name' => 'Dr. Aisha Verma',
                    'branch_schedule_millisOfDay' => [
                        'gt' => 0,
                        'lt' => 86400000 // full day
                    ]
                ],
                [
                    'schedules' => [
                        [
                            'id' => 'sched_002',
                            'creation_timestamp' => time() - 1000,
                            'last_updated_timestamp' => time(),
                            'entity_type' => 'doctor',
                            'entity_id' => $filters['doctor_id'] ?? 'doc_001',
                            'doctor_department_id' => $filters['department_id'] ?? 'dep_001',
                            'doctor_specialization_id' => $filters['specialization_id'] ?? 'spec_001',
                            'doctor_service_id' => $filters['service_id'] ?? 'srv_001',
                            'day_of_week' => 'Monday',
                            'day_of_week_number' => 1,
                            'start_time_utc' => '2025-07-17T16:20:35.123Z',
                            'end_time_utc' => '2025-07-17T16:20:35.123Z',
                            'schedule_range_millisOfDay' => [
                                'gt' => 16200000, // 4:30 AM
                                'lt' => 27000000  // 7:30 AM
                            ],
                            'start_time' => '2025-07-17T16:20:35.123Z',
                            'end_time' => '2025-07-17T18:20:35.123Z',
                            'start_time_epoch' => (string) strtotime('2025-07-17 10:00:00'),
                            'end_time_epoch' => (string) strtotime('2025-07-17 13:00:00')
                        ]
                    ],
                    'blockers' => [
                        [
                            'blocked_type' => 'appointment',
                            'blocked_type_id' => 'block_001',
                            'start_time_epoch' => strtotime('2025-07-17 10:30:00') * 1000,
                            'end_time_epoch' => strtotime('2025-07-17 11:30:00') * 1000,
                            'blocked_range' => [
                                'gt' => 37800000,
                                'lt' => 39600000
                            ],
                            'reason' => 'Patient Follow-up',
                            'patient_id' => 'pat_001',
                            'patient_name' => 'Rahul Sharma',
                            'patient_mobile' => '9876543210',
                            'patient_email' => 'rahul@example.com',
                            'patient_gender' => 'Male',
                            'patient_age' => '30',
                            'patient_blood_group' => 'B+',
                            'symptoms' => 'Rash and itching',
                            'appointment_status' => 'confirmed',
                            'image_id' => 'img_001',
                            'patient_code' => 'P0001',
                            'service_name' => 'Skin Consultation'
                        ]
                    ],
                    'holidays_leaves' => [
                        [
                            'id' => 'leave_001',
                            'entity_type' => 'doctor',
                            'entity_id' => $filters['doctor_id'] ?? 'doc_001',
                            'start_time' => '2025-07-18T00:00:00Z',
                            'end_time' => '2025-07-18T23:59:59Z',
                            'is_active' => true,
                            'start_time_epoch' => strtotime('2025-07-18 00:00:00'),
                            'end_time_epoch' => strtotime('2025-07-18 23:59:59')
                        ]
                    ],
                    'service_types' => 'CONSULTANT',
                    'doctor_id' => $filters['doctor_id'] ?? 'doc_002',
                    'doctor_name' => 'Dr. Steven Strange',
                    'branch_schedule_millisOfDay' => [
                        'gt' => 0,
                        'lt' => 86400000 // full day
                    ]
                ]
            ],
            'branch_schedule_millisOfDay' => [
                'gt' => 0,
                'lt' => 86400000
            ]
        ];

        return response()->json($dummyResponse);
    }

    public function getCountries()
    {
        $countries = [
            ['id' => 1, 'country_name' => 'India', 'iso_code' => 'IN'],
            ['id' => 2, 'country_name' => 'United States', 'iso_code' => 'US'],
            ['id' => 3, 'country_name' => 'United Kingdom', 'iso_code' => 'GB'],
            ['id' => 4, 'country_name' => 'Canada', 'iso_code' => 'CA'],
            ['id' => 5, 'country_name' => 'Australia', 'iso_code' => 'AU'],
            ['id' => 6, 'country_name' => 'Germany', 'iso_code' => 'DE'],
            ['id' => 7, 'country_name' => 'France', 'iso_code' => 'FR'],
            ['id' => 8, 'country_name' => 'Japan', 'iso_code' => 'JP'],
            ['id' => 9, 'country_name' => 'Brazil', 'iso_code' => 'BR'],
            ['id' => 10, 'country_name' => 'South Africa', 'iso_code' => 'ZA'],
        ];

        return response()->json($countries);
    }

    public function getHospitalNames(Request $request)
    {
        $profileIds = $request->all();

        if (!is_array($profileIds) || empty($profileIds)) {
            return response()->json(['error' => 'Invalid profile ID list'], 400);
        }

        // Dummy employee-hospital mapping
        $dummyHospitals = [
            '1000' => [
                'id' => '1000',
                'hospital_id' => 101,
                'hospital' => [
                    'id' => 101,
                    'name' => 'Sunrise Hospital',
                    'iso_code' => 'IN'
                ]
            ],
            'profile2' => [
                'id' => 'profile2',
                'hospital_id' => 102,
                'hospital' => [
                    'id' => 102,
                    'name' => 'Green Valley Clinic',
                    'iso_code' => 'US'
                ]
            ],
            'profile3' => [
                'id' => 'profile3',
                'hospital_id' => 103,
                'hospital' => [
                    'id' => 103,
                    'name' => 'Global Health Center',
                    'iso_code' => 'UK'
                ]
            ]
        ];

        $result = [];

        foreach ($profileIds as $id) {
            if (isset($dummyHospitals[$id])) {
                $result[] = $dummyHospitals[$id];
            }
        }

        return response()->json($result);
    }

    public function createWing(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'wing_name' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $prev = Wing::select('id')->where('id', $request['id'])->first();

        if ($prev) {
            $prev->wing_name = $request['wing_name'];
            $prev->save();
        } else {
            Wing::create([
                'branch_id'       => $branchId,
                'wing_name'       => $request['wing_name'],
            ]);
        }

        return response()->json(['message' => 'Wing inserted successfully'], 201);
    }

    public function createBuilding(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'building_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();

        foreach ($data as $building) {
            Building::create([
                'branch_id'       => $branchId,
                'wing_name'       => $building['wing_name'] ?? null,
                'building_name'   => $building['building_name'] ?? null,
                'building_number' => $building['building_number'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Buildings inserted successfully'], 201);
    }

    public function updateBuilding(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'building_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $upd = Building::where('id', $request['building_id'])  // Fix the key name accordingly
            ->update([
                'building_name'   => $request['building_name'] ?? null,
                'building_number' => $request['building_number'] ?? null,
            ]);

        return response()->json(['message' => 'Buildings updated successfully'], 201);
    }

    public function createBulkBuilding(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        $data = $request->all();

        $validator = Validator::make([
            'buildings' => $data
        ], [
            'buildings' => 'required|array',
            'buildings.*.wing_name' => 'present',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($data as $key => $building) {

            if ($key == 0) {
                $check = 1;
                if (!isset($building['wing_name'])) {
                    $wing = Wing::where('wing_name', 'NO WING')->where('branch_id', $branchId)->first();
                    if ($wing) $check = 0;
                }
                if ($check) {
                    $wing = Wing::create([
                        'branch_id'       => $branchId,
                        'wing_name'       => $building['wing_name'] ? $building['wing_name'] : 'NO WING',
                    ]);
                }
            }

            Building::create([
                'branch_id'       => $branchId,
                'wing_id'       => $wing->id,
                'wing_name'       => $building['wing_name'] ?? 'NO WING',
                'building_name'   => $building['building_name'] ?? null,
                'building_number' => $building['building_number'] ?? null,
            ]);
        }


        return response()->json(['message' => 'Buildings inserted successfully'], 201);
    }

    public function buildingSearch(Request $request)
    {

        if ($request['wing_name']) {
            $buildingData = Building::with(['branch', 'wing'])
                ->where('wing_name', $request['wing_name'])
                ->get();
        } else {
            $buildingData = Building::with([
                'branch',
            ])->get();
        }

        return response()->json([
            'total_count'     => $buildingData->count(),
            'message' => 'successfully',
            'search_response' => $buildingData,
        ]);
    }

    public function buildingWingSearch(Request $request)
    {

        $wingData = Wing::with([
            'branch',
            'buildings'
        ])->get();

        // $companyData->each(function ($company) {
        //     $company->setAttribute('beneficiary', $company->details);
        //     $company->setAttribute('name', $company->legal_facility_name);
        //     unset($company->details);
        // });

        return response()->json([
            'total_count'     => $wingData->count(),
            'message' => 'successfully',
            'search_response' => $wingData,
        ]);
    }

    public function floorSearch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'building_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $floorData = Floor::with(['building'])
            ->where('building_id', $request['building_id'])
            ->get();

        return response()->json([
            'total_count'     => $floorData->count(),
            'message' => 'successfully',
            'search_response' => $floorData,
        ]);
    }

    public function createFloor(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        if ($request->isMethod('put')) {

            $validator = Validator::make($request->all(), [
                'building_id' => 'required',
                'floor_name' => 'required',
                'floor_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Floor::where('id', $request['floor_id'])  // Fix the key name accordingly
                ->update([
                    'floor_name'   => $request['floor_name'] ?? null,
                    'floor_number' => $request['floor_number'] ?? null,
                ]);

            $id = $request['floor_id'];
        } else {

            $validator = Validator::make($request->all(), [
                'building_id' => 'required',
                'floor_name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cr = Floor::create([
                'branch_id'       => $branchId,
                'building_id'       => $request['building_id'] ?? null,
                'floor_name'   => $request['floor_name'] ?? null,
                'floor_number' => $request['floor_number'] ?? null,
            ]);

            $id = $cr->id;
        }

        $floorData = Floor::with(['building'])
            ->where('id', $id)
            ->first();

        return response()->json(['data' => $floorData], $request->isMethod('put') ? 202 : 201);
    }

    public function roomSearch(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'floor_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $roomData = Room::with(['floor'])
            ->where('floor_id', $request['floor_id'])
            ->get();

        return response()->json([
            'total_count'     => $roomData->count(),
            'message' => 'successfully',
            'search_response' => $roomData,
        ]);
    }

    public function createRoom(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        if ($request->isMethod('put')) {

            $validator = Validator::make($request->all(), [
                'floor_id' => 'required',
                'room_name' => 'required',
                'room_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Room::where('id', $request['room_id'])  // Fix the key name accordingly
                ->update([
                    'room_name'   => $request['room_name'] ?? null,
                    'room_number' => $request['room_number'] ?? null,
                ]);

            $id = $request['room_id'];
        } else {

            $validator = Validator::make($request->all(), [
                'floor_id' => 'required',
                'room_name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cr = Room::create([
                'branch_id'       => $branchId,
                'floor_id'       => $request['floor_id'] ?? null,
                'room_name'   => $request['room_name'] ?? null,
                'room_number' => $request['room_number'] ?? null,
            ]);

            $id = $cr->id;


            $floorData = Room::with(['floor'])
                ->where('id', $id)
                ->first();

            return response()->json(['data' => $floorData], $request->isMethod('put') ? 202 : 201);
        }
    }

    public function cabinSearch(Request $request)
    {

        if (isset($request['room_id'])) {
            $cabinData = Cabin::with(['room'])
                ->where('room_id', $request['room_id'])
                ->get();
        } else {
            $branchId = $request->header('X-TREINT-BRANCH-ID');
            $cabinData = Cabin::with(['room'])->where('branch_id', $branchId)->get();
        }


        return response()->json([
            'total_count'     => $cabinData->count(),
            'message' => 'successfully',
            'search_response' => $cabinData,
        ]);
    }

    public function createCabin(Request $request)
    {

        $branchId = $request->header('X-TREINT-BRANCH-ID');

        if (!$branchId) {
            return response()->json([
                'message' => 'Branch Id not found',
                'status' => false
            ], 422);
        }

        if ($request->isMethod('put')) {

            $validator = Validator::make($request->all(), [
                'room_id' => 'required',
                'cabin_name' => 'required',
                'cabin_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            Cabin::where('id', $request['cabin_id'])  // Fix the key name accordingly
                ->update([
                    'cabin_name'   => $request['cabin_name'] ?? null,
                    'cabin_number' => $request['cabin_number'] ?? null,
                ]);

            $id = $request['cabin_id'];
        } else {

            $validator = Validator::make($request->all(), [
                'room_id' => 'required',
                'cabin_name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $cr = Cabin::create([
                'branch_id'       => $branchId,
                'room_id'       => $request['room_id'] ?? null,
                'cabin_name'   => $request['cabin_name'] ?? null,
                'cabin_number' => $request['cabin_number'] ?? null,
            ]);

            $id = $cr->id;
        }

        $cabinData = Cabin::with(['room'])
            ->where('id', $id)
            ->first();

        return response()->json(['data' => $cabinData], $request->isMethod('put') ? 202 : 201);
    }
}
