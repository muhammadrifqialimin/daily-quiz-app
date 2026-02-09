<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Resources\QuizResource;
use App\Services\QuizService;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function index()
    {
        $data = $this->quizService->getTodayQuizzes();

        return response()->json([
            'status' => true,
            'message' => 'Daftar soal hari ini',
            'data' => QuizResource::collection($data),
        ]);
    }

    public function store(StoreQuizRequest $request)
    {
        $quiz = $this->quizService->createQuiz($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Soal berhasil ditambahkan',
            'data' => new QuizResource($quiz),
        ], 201);
    }
}
