<?php

namespace App\Services;

use App\Models\Quiz;

class QuizService
{
    public function createQuiz(array $data)
    {
        return Quiz::create($data);
    }

    public function getTodayQuizzes()
    {
        return Quiz::WhereDate('active_date', now())
            ->inRandomOrder()
            ->limit(5)
            ->get();
    }
}