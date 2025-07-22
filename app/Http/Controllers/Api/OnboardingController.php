<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Mail\SendOtpMail;
use App\Models\Document;
use App\Models\HospitalInfo;
use App\Models\HospitalRegistration;
use App\Models\Owner;
use App\Models\PendingUser;
use App\Models\User;
use App\Models\UserInfo;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OnboardingController extends BaseController
{
    use ApiResponse;

    public function verifyEmail(Request $request)
    {

        $email = $request['email'];
        if (!$email) {
            return $this->error('Email not found', 400);
        }

        $data = User::select('id')->where('email', $email)->where('user_type', 'ONBOARDER')->first();

        if ($data) {
            return response()->json([
                'status' => true,
                'is_onboarding_profile_exists' => true,
                'email' => $email
            ]);
        } else {
            return response()->json([
                'status' => true,
                'is_onboarding_profile_exists' => false,
                'email' => $email
            ]);
        }
    }

    public function sendEmailOtp(Request $request)
    {

        try {

            $request->validate([
                'email' => 'nullable|email',
                'mobile' => 'nullable|string|regex:/^[0-9]{7,15}$/',
                'country_code' => 'nullable|string|max:5'
            ]);

            $otp = rand(100000, 999999);

            $pendingUser = PendingUser::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'is_verified' => false,
                    'expires_at' => now()->addMinutes(15),
                ]
            );


            // Send OTP via Mail or Notification
            $data = [
                'otp' => $otp
            ];
            $toEmail = $request->email;
            Mail::to($toEmail)->send(new SendOtpMail($data));

            return response()->json([
                'success' => true,
                'referenceId' => $pendingUser->id,
                'email' => $request->email,
                'mobile' => $request->mobile
            ]);
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function sendMobileOtp(Request $request)
    {

        try {

            $request->validate([
                'company_name' => 'nullable',
                'mobile' => 'nullable|string|regex:/^[0-9]{7,15}$/',
                'country_code' => 'nullable|string|max:5'
            ]);

            $otp = rand(100000, 999999);

            $pendingUser = PendingUser::updateOrCreate(
                ['email' => $request->mobile],
                [
                    'otp' => $otp,
                    'is_verified' => false,
                    'expires_at' => now()->addMinutes(15),
                ]
            );


            // Send OTP via Mail or Notification
            // Mail::to($request->email)->send(new SendOtpMail($otp));

            return response()->json([
                'success' => true,
                'referenceId' => $pendingUser->id,
                'mobile' => $request->mobile
            ]);
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function reSendMobileOtp(Request $request)
    {

        try {

            $request->validate([
                'company_name' => 'nullable',
                'mobile' => 'nullable|string|regex:/^[0-9]{7,15}$/',
                'country_code' => 'nullable|string|max:5'
            ]);

            $otp = rand(100000, 999999);

            $pendingUser = PendingUser::updateOrCreate(
                ['email' => $request->mobile],
                [
                    'otp' => $otp,
                    'is_verified' => false,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            // Send OTP via Mail or Notification
            // Mail::to($request->email)->send(new SendOtpMail($otp));

            return response()->json([
                'success' => true,
                'referenceId' => $pendingUser->id,
                'mobile' => $request->mobile
            ]);
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function reSendEmailOtp(Request $request)
    {

        try {

            $request->validate([
                'email' => 'nullable|email',
                'mobile' => 'nullable|string|regex:/^[0-9]{7,15}$/',
                'country_code' => 'nullable|string|max:5'
            ]);

            $otp = rand(100000, 999999);

            $pendingUser = PendingUser::updateOrCreate(
                ['email' => $request->email],
                [
                    'otp' => $otp,
                    'is_verified' => false,
                    'expires_at' => now()->addMinutes(15),
                ]
            );

            // Send OTP via Mail or Notification
            $data = [
                'otp' => $otp
            ];
            $toEmail = $request->email;
            Mail::to($toEmail)->send(new SendOtpMail($data));

            return response()->json([
                'success' => true,
                'referenceId' => $pendingUser->id,
                'email' => $request->email,
                'mobile' => $request->mobile
            ]);
        } catch (\Throwable $e) {

            $this->reportException(__CLASS__ . "/" . __FUNCTION__, $e->getMessage());
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'referenceId' => 'required',
            'otp' => 'required|string|max:6',
        ]);

        $pendingUser = PendingUser::where('id', $request->referenceId)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$pendingUser) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Invalid or expired OTP.'
            ]);
        }

        if ($pendingUser->is_verified) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'OTP already verified.'
            ]);
        }

        $pendingUser->update(['is_verified' => true]);

        // Delete after verification
        // $pendingUser->delete();

        return response()->json([
            'success' => true,
            'valid' => true,
        ]);
    }

    public function register(Request $request)
    {

        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'user_type' => 'required|string',
        ]);

        $user = User::create([
            'email' => $request->email,
            'name' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'INITIATED', // default onboarding status
            'role' => $request->role,
            'user_type' => $request->user_type,
        ]);

        $user->reference_id = $this->encryptId($user->id);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'user_id' => $user->id,
        ], 201);
    }

    public function getOnboardingStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::where('id', $request->user_id)
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid User Id'
            ], 404);
        }

        // Determine response based on status
        $status = $user->status;
        $signupData = []; // replace with actual data if needed
        $id = $user->id;   // or generate another tracking ID

        $signupData = UserInfo::where('user_id', $user->id)->first(); // Example
        $hospitalReg = HospitalRegistration::where('user_id', $user->id)->first(); // Example
        $hospitalInfo = HospitalInfo::where('user_id', $user->id)->first(); // Example

        return response()->json([
            'status' => $status,
            'id' => $id,
            'trackingId' => $user->reference_id,
            'next_step' => $user->next_step,
            'signup' => $signupData,
            'hospital_registration' => $hospitalReg,
            'company_information' => $hospitalInfo
        ]);
    }

    public function updateUserInfo(Request $request, $userId)
    {
        $validated = $request->validate([
            'employee_name' => 'nullable|string',
            'employee_id' => 'nullable|string',
            'adhaar_id' => 'nullable|string',
            'id_proof' => 'nullable|string',
            'employee_id_doc_id' => 'nullable|string',
            'adhaar_id_doc_id' => 'nullable|string',
            'adhaar_id_back_doc_id' => 'nullable|string',
            'id_proof_doc_id' => 'nullable|string',
        ]);

        $user = User::findOrFail($userId);

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid User Id'
            ]);
        }

        $userInfo = UserInfo::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        $HospitalRegData = HospitalRegistration::where('user_id', $user->id)->first(); // Example

        $user->next_step = 'HOSPITAL_REGISTRATION';
        $user->save();

        return response()->json([
            'message' => 'User Info updated successfully.',
            'data' => [
                'user_info' => $userInfo,
                'hospital_registration' => $HospitalRegData, // Replace with actual data if available
                'status' => 'COMPLETED',         // Simulate onboardingStatus
            ],
        ]);
    }

    public function updateHospitalRegistration(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'country_code' => 'required|string|max:5',
        ]);

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid User Id'
            ]);
        }

        $hospital = HospitalRegistration::updateOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $request->company_name,
                'mobile' => $request->mobile,
                'country_code' => $request->country_code,
            ]
        );

        $hospitalInfoData = HospitalInfo::where('user_id', $user->id)->first();

        $user->next_step = 'COMPANY_INFORMATION';
        $user->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Hospital registration data saved.',
            'company_information' => $hospitalInfoData,
            'status' => $user->status ?? 'PENDING'  // assuming you track onboarding status on `users` table
        ]);
    }

    public function updateHospitalInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'cin' => 'required|string|max:255',
            'legal_facility_name' => 'required|string|max:255',
            'cin_doc_id' => 'required|string',
            'company_pan' => 'required|string|max:255',
            'company_pan_doc_id' => 'required|string',
            'license_number' => 'required|string|max:255',
            'license_issued_year' => 'required|string', // should be formatted dd/MM/yyyy
            'license_doc_id' => 'required|string',
            'tax_id' => 'required|string|max:255',
            'tax_doc_id' => 'required|string',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'pincode' => 'required|integer',
            'province' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
        ]);

        $validated['license_issued_year'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['license_issued_year'])->format('Y-m-d');


        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid User Id'
            ]);
        }

        $hospitalInfo = HospitalInfo::updateOrCreate(
            ['user_id' => $id],
            $validated
        );

        $ownerInfo = Owner::where('user_id', $user->id)->first();

        $user->next_step = 'OWNER_INFORMATION';
        $user->save();

        // Mock data to simulate frontend usage
        return response()->json([
            'status' => $user->status ?? 'PENDING', // or 'DECLINED' to simulate condition
            'owner_information' => $ownerInfo
        ]);
    }

    public function updateOwnerInfo(Request $request, $id)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string|max:255',
            'owner_mobile_number' => 'required|string',
            'owner_email' => 'required|string|max:255',
            'owner_designation' => 'required|string',
            'consent' => 'required',
        ]);

        $user = User::findOrFail($id);

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid User Id'
            ]);
        }

        $ownerInfo = Owner::updateOrCreate(
            ['user_id' => $id],
            $validated
        );

        $user->status = 'UNDER_REVIEW';
        $user->save();

        // Mock data to simulate frontend usage
        return response()->json([
            'id' => $user->reference_id
        ]);
    }

    public function trackStatus($regId)
    {

        $id = $this->decryptId($regId);

        $user = $id ? User::find($id) : null;

        if (!$user) {
            return response()->json([
                'status' => false, // or 'DECLINED' to simulate condition
                'message' => 'Invalid Registration Id'
            ], 404);
        }

        return response()->json([
            'status' => $user->status, // or 'DECLINED' to simulate condition
        ]);
    }

}
