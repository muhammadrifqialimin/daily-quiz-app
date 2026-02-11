<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'category',
        'total_correct',
        'total_questions',
        'score',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array', 
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}