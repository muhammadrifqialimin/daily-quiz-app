<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Result;

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
        $student = Student::create($request->all());
        
        return response()->json([
            'message' => 'Siswa berhasil ditambahkan', 
            'data' => $student
        ]);
    }
}