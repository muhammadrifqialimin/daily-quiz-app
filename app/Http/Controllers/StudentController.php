<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Result;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $student = Student::where('name', $request->name)
            ->where('password', $request->password)
            ->first();

        if ($student) {
            
            $todayResult = Result::where('student_id', $student->id)
                ->whereDate('created_at', now())
                ->first();

            $responseData = [
                'student' => $student,
                'has_completed' => false,
                'result' => null
            ];

            if ($todayResult) {
                $responseData['has_completed'] = true;
                $responseData['result'] = $todayResult;
            }

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'data' => $responseData
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Nama atau password salah',
            ], 401);
        }
    } 

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'class_name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $student = Student::create([
            'name' => $request->name,
            'class_name' => $request->class_name,
            'category' => $request->category ?? 'Umum',
            'password' => $request->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Siswa berhasil didaftarkan',
            'data' => $student
        ]);
    }
    public function profile($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $results = Result::where('student_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        $totalQuiz = $results->count();
        $highestScore = $results->max('score') ?? 0;
        $averageScore = $totalQuiz > 0 ? round($results->avg('score'), 1) : 0;

        $rank = "Pemula";
        if ($averageScore >= 90) $rank = "Grand Master 🏆";
        else if ($averageScore >= 80) $rank = "Expert 💎";
        else if ($averageScore >= 60) $rank = "Senior ⭐";

        $chartData = Result::where('student_id', $id)
                        ->orderBy('created_at', 'asc') // 
                        ->take(7)
                        ->get()
                        ->map(function($res) {
                            return [
                                'date' => $res->created_at->format('d/m'),
                                'score' => $res->score,
                                'correct' => $res->total_correct,
                                'wrong' => $res->total_questions - $res->total_correct
                            ];
                        });

        return response()->json([
            'status' => true,
            'data' => [
                'name' => $student->name,
                'class_name' => $student->class_name ?? 'Kelas 10A',
                'profile_image' => $student->profile_image,
                'stats' => [
                    'total_quizzes' => $totalQuiz,
                    'highest_score' => $highestScore,
                    'average_score' => $averageScore,
                    'rank' => $rank
                ],
                'history' => $results,
                'chart_data' => $chartData
            ]
        ]);
    }
}