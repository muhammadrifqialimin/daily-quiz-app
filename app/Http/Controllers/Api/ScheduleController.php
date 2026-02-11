<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Student;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil ID siswa dari request
        $studentId = $request->student_id;
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        // 2. Ambil jadwal Sesuai KELAS Siswa & Hari Ini
        $now = Carbon::now();
        
        $schedules = Schedule::where('class_name', $student->class_name)
            ->orderBy('start_time', 'asc') // Urutkan dari yang paling pagi
            ->get()
            ->map(function($schedule) use ($now) {
                // Logika Status Ujian
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                
                $status = 'upcoming'; // Belum mulai
                if ($now->between($start, $end)) {
                    $status = 'active'; // Sedang berlangsung
                } elseif ($now->gt($end)) {
                    $status = 'finished'; // Sudah lewat
                }

                return [
                    'id' => $schedule->id,
                    'subject' => $schedule->subject,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'full_end_time' => $schedule->end_time,
                    'date' => $start->format('d M Y'),
                    'status' => $status, 
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $schedules
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'class_name' => 'required', // Contoh: "12 IPA 1"
            'subject'    => 'required', // Contoh: "Sejarah"
            'start_time' => 'required|date_format:Y-m-d H:i:s', // Format Tahun-Bulan-Tanggal Jam:Menit:Detik
            'end_time'   => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Simpan ke Database
        $schedule = Schedule::create([
            'class_name' => $request->class_name,
            'subject'    => $request->subject,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil dibuat!',
            'data' => $schedule
        ]);
    }
}