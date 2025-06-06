<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DiagnosisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    
});