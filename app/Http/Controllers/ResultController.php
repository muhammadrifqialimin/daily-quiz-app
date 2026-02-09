<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Result;

class ResultController extends Controller
{
    public function submit(Request $request)
    {
        $studentId = $request->student_id;
        $category = $request->category;
        $studentAnswers = $request->answers; 

        $totalCorrect = 0;
        $totalQuestions = count($studentAnswers);

        foreach ($studentAnswers as $quizId => $answer) {
            $quiz = Quiz::find($quizId);
            
            if ($quiz && strtolower($quiz->answer) == strtolower($answer)) {
                $totalCorrect++;
            }
        }

        $finalScore = ($totalQuestions > 0) ? round(($totalCorrect / $totalQuestions) * 100) : 0;

        Result::create([
            'student_id' => $studentId,
            'category' => $category,
            'total_correct' => $totalCorrect,
            'total_questions' => $totalQuestions,
            'score' => $finalScore
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Nilai berhasil disimpan',
            'score' => $finalScore,
            'correct' => $totalCorrect,
            'total' => $totalQuestions
        ]);
    }
}