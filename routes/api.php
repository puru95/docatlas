<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\SpecializationController;
use App\Http\Controllers\Api\TreatmentManagerController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::prefix('auth/v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::post('/oauth-token',    [AuthController::class, 'authLogin']);
    Route::post('/refresh-token',    [AuthController::class, 'refreshToken']);
    Route::post('/verify-email', [OnboardingController::class, 'verifyEmail']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/user',   [AuthController::class, 'user']);
        Route::get('/mediassist/users/{userId?}', [DiagnosisController::class, 'getUserProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('v1')->group(function () {
    Route::post('/otp/email/send-otp', [OnboardingController::class, 'sendEmailOtp']);
    Route::post('/otp/email/resend-otp', [OnboardingController::class, 'reSendEmailOtp']);
    Route::post('/otp/phone/resend-otp', [OnboardingController::class, 'reSendMobileOtp']);
    Route::post('/otp/phone/send-otp', [OnboardingController::class, 'sendMobileOtp']);
    Route::post('/otp/email/validate-otp', [OnboardingController::class, 'verifyOtp']);
    Route::post('/otp/phone/validate-otp', [OnboardingController::class, 'verifyOtp']);
    Route::post('/register', [OnboardingController::class, 'register']);
    Route::post('/onboarding', [OnboardingController::class, 'getOnboardingStatus']);
    Route::put('/onboarding/{user}/signup', [OnboardingController::class, 'updateUserInfo']);
    Route::put('onboarding/{id}/hospitalregistration', [OnboardingController::class, 'updateHospitalRegistration']);
    Route::put('onboarding/{id}/companyInformation', [OnboardingController::class, 'updateHospitalInfo']);
    Route::put('onboarding/{id}/ownerInformation', [OnboardingController::class, 'updateOwnerInfo']);
    Route::post('/documents/upload/image', [DocumentController::class, 'uploadImage']);
    Route::post('/image/upload', [DocumentController::class, 'uploadDocImage']);
    Route::get('/onboarding/{id}', [OnboardingController::class, 'trackStatus']);
    Route::get('/maps/api/countries', [BranchController::class, 'getCountries']);
    Route::post('query/employee/profile', [BranchController::class, 'getHospitalNames']);
    Route::get('/documents/by_ids', [DocumentController::class, 'getDocumentsByIds']);

});

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::post('/medicines/search', [AnalysisController::class, 'getMedicinesData']);
    Route::post('/diseases/search', [DiagnosisController::class, 'getDiseaseSearchData']);
    Route::post('/salts/search', [AnalysisController::class, 'getSaltsData']);
    Route::post('/symptoms/search', [DiagnosisController::class, 'getSymptomsData']);
    Route::post('/getDiseaseBySymptoms', [DiagnosisController::class, 'getDiseaseBySymptoms']);
    Route::post('/getDiseaseDetails', [DiagnosisController::class, 'getDiseaseDetails']);
    Route::post('/getQuestionsByOpenAI', [DiagnosisController::class, 'getQuestionsByOpenAI']);
    Route::post('/submitDiagnosisAnswers', [DiagnosisController::class, 'submitDiagnosisAnswers']);


    Route::delete('/session', [AuthController::class, 'logout']);
    
    Route::post('/branch/search', [BranchController::class, 'listBranches']);
    Route::post('/query/branch/search', [BranchController::class, 'list']);
    
    Route::get('/query/user/profile/{id}', [AuthController::class, 'show']);
    
    Route::post('/query/employee/search', [BranchController::class, 'getEmployeeList']);
    Route::post('/query/patient/search', [BranchController::class, 'getPatientList']);
    Route::post('/query/doctor/search-list', [BranchController::class, 'getDoctorList']);
    Route::post('/query/calendar/search', [BranchController::class, 'getDoctorSchedules']);
    Route::post('/query/treatments/treatment-view', [TreatmentManagerController::class, 'fetchAppointments']);
    Route::get('/query/treatments/treatment-record', [TreatmentManagerController::class, 'getMedicalRecord']);
    Route::post('/medical-history/search', [TreatmentManagerController::class, 'fetchMedicalHistory']);
    Route::post('/pulse/create-guest-patient', [TreatmentManagerController::class, 'store']);
    Route::post('/query/treatments/plan-submit', [TreatmentManagerController::class, 'storee']);
    Route::post('/treatment-history', [TreatmentManagerController::class, 'getTreatmentData']);

    Route::post('/dashboard/revenue/stat', [DashboardController::class, 'getTotalCount']);
    Route::post('/dashboard/revenue', [DashboardController::class, 'getRevenueData']);
    Route::post('/dashboard/appointment', [DashboardController::class, 'getAppointmentData']);
    Route::post('/dashboard/workload', [DashboardController::class, 'getWorkloadData']);

    Route::post('/command/branch', [BranchController::class, 'createBranch']);
    Route::put('/building/wing', [BranchController::class, 'createWing']);
    Route::put('/building/{id}', [BranchController::class, 'updateBuilding']);
    Route::post('/building/bulk', [BranchController::class, 'createBulkBuilding']);
    Route::post('/building/search', [BranchController::class, 'buildingSearch']);
    Route::post('/building/wing/search', [BranchController::class, 'buildingWingSearch']);

    Route::post('/floor/search', [BranchController::class, 'floorSearch']);
    Route::post('/floor', [BranchController::class, 'createFloor']);
    Route::put('/floor/{id}', [BranchController::class, 'createFloor']);

    Route::post('/roomOrSection/search', [BranchController::class, 'roomSearch']);
    Route::post('/roomOrSection', [BranchController::class, 'createRoom']);
    Route::put('/roomOrSection/{id}', [BranchController::class, 'createRoom']);

    Route::post('/cabinOrDesk/search', [BranchController::class, 'cabinSearch']);
    Route::post('/cabinOrDesk', [BranchController::class, 'createCabin']);
    Route::put('/cabinOrDesk/{id}', [BranchController::class, 'createCabin']);

    Route::post('/department/search', [DepartmentController::class, 'getDepartmentList']);
    Route::post('/command/department', [DepartmentController::class, 'store']);
    Route::put('/department/{depId}', [DepartmentController::class, 'edit']);
    Route::put('/department/{depId}/structure', [DepartmentController::class, 'editStructure']);
    Route::post('/query/department/search', [DepartmentController::class, 'getDepartmentList']);
    Route::get('/query/department/structure/{id}', [DepartmentController::class, 'getStructure']);
    Route::get('/command/department/{deptId}/cabin/{cabinId}/validate', [DepartmentController::class, 'getStructure']);

    Route::post('/query/specialization/search', [SpecializationController::class, 'getSpecializationList']);
    Route::post('/command/specialization', [SpecializationController::class, 'store']);
    Route::put('/specialization/{specId}', [SpecializationController::class, 'edit']);

    Route::post('/query/service/search', [ServiceController::class, 'getServiceList']);
    Route::post('/command/service', [ServiceController::class, 'store']);

    Route::post('/command/admin', [EmployeeController::class, 'createAdmin']);

    Route::post('/kyc', [CompanyController::class, 'store']);
    Route::get('/query/company', [CompanyController::class, 'getInfo']);
    Route::post('/company/search', [CompanyController::class, 'getAll']);

});
