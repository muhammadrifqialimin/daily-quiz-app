<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ResultController;

Route::prefix('v1')->group(function () {
    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::post('/login', [StudentController::class, 'login']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::post('/submit-quiz', [ResultController::class, 'submit']);
    Route::get('/students/{id}/profile', [App\Http\Controllers\StudentController::class, 'profile']);
    Route::get('/results/{id}', [App\Http\Controllers\ResultController::class, 'show']);
    Route::get('/schedules', [App\Http\Controllers\Api\ScheduleController::class, 'index']);
    Route::post('/schedules', [App\Http\Controllers\Api\ScheduleController::class, 'store']);
});