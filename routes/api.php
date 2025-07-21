<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\OnboardingController;

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
    
    Route::post('/query/branch/search', [BranchController::class, 'list']);
    Route::post('/cabinOrDesk/search', [BranchController::class, 'getCabinDetails']);
    Route::get('/query/user/profile/{id}', [AuthController::class, 'show']);
    Route::post('/query/department/search', [BranchController::class, 'getDepartmentList']);
    Route::post('/department/search', [BranchController::class, 'getDepartmentList']);
    Route::post('/query/specialization/search', [BranchController::class, 'getSpecializationList']);
    Route::post('/query/service/search', [BranchController::class, 'getServiceList']);
    Route::post('/query/employee/search', [BranchController::class, 'getEmployeeList']);
    Route::post('/query/patient/search', [BranchController::class, 'getPatientList']);
    Route::post('/query/doctor/search-list', [BranchController::class, 'getDoctorList']);
    Route::post('/query/calendar/search', [BranchController::class, 'getDoctorSchedules']);
});
