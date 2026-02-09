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
});