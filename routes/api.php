<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;

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

    Route::middleware('auth:api')->group(function () {
        Route::get('/user',   [AuthController::class, 'user']);
        Route::get('/mediassist/users/{userId?}', [DiagnosisController::class, 'getUserProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
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
