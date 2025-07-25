<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function getTotalCount(Request $request)
    {
        return response()->json([
            'appointment' => ['count' => 123],
            'doctor'      => ['count' => 17],
            'patient'     => ['count' => 200],
            'revenue'     => ['sum'   => 155000],
        ]);
    }

    public function getRevenueData(Request $request)
    {
        return response()->json([
            [
                'bucketName' => 'Branch A',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'total_amount' => 15,
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'total_amount' => 12,
                    ]
                ]
            ],
            [
                'bucketName' => 'Branch B',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'total_amount' => 5,
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'total_amount' => 8,
                    ]
                ]
            ]
        ]);
    }

    public function getAppointmentData(Request $request)
    {
        return response()->json([
            [
                'bucketName' => 'Branch A',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'total_appointment' => 20,
                        'scheduled_appointment' => 10,
                        'completed_appointment' => 5,
                        'ongoing_appointment' => 2,
                        'cancelled_appointment' => 1,
                        'expired_appointment' => 2
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'total_appointment' => 15,
                        'scheduled_appointment' => 5,
                        'completed_appointment' => 5,
                        'ongoing_appointment' => 2,
                        'cancelled_appointment' => 1,
                        'expired_appointment' => 2
                    ]
                ]
            ],
            [
                'bucketName' => 'Branch B',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'total_appointment' => 30,
                        'scheduled_appointment' => 10,
                        'completed_appointment' => 15,
                        'ongoing_appointment' => 2,
                        'cancelled_appointment' => 1,
                        'expired_appointment' => 2
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'total_appointment' => 28,
                        'scheduled_appointment' => 9,
                        'completed_appointment' => 14,
                        'ongoing_appointment' => 2,
                        'cancelled_appointment' => 1,
                        'expired_appointment' => 2
                    ]
                ]
            ]
        ]);
    }

    public function getWorkloadData(Request $request)
    {
        return response()->json([
            [
                'bucketName' => 'Branch A',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'worktime' => 15,
                        'blockedtime' => 10,
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'worktime' => 12,
                        'blockedtime' => 8,
                    ]
                ]
            ],
            [
                'bucketName' => 'Branch B',
                'buckets' => [
                    [
                        'key_as_string' => '2025-07-01',
                        'worktime' => 20,
                        'blockedtime' => 5,
                    ],
                    [
                        'key_as_string' => '2025-07-02',
                        'worktime' => 18,
                        'blockedtime' => 7,
                    ]
                ]
            ]
        ]);
    }
}
