<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Result extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'student_id', 'exam_id', 'total_marks', 'full_marks',
        'percentage', 'gpa', 'grade', 'division', 'is_passed', 'rank', 'subject_details',
    ];

    protected $casts = [
        'total_marks'     => 'float',
        'full_marks'      => 'float',
        'percentage'      => 'float',
        'gpa'             => 'float',
        'is_passed'       => 'boolean',
        'subject_details' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function getGradeBadgeColorAttribute(): string
    {
        return match ($this->grade) {
            'A+'    => 'bg-green-600',
            'A'     => 'bg-green-500',
            'A-'    => 'bg-green-400',
            'B+'    => 'bg-blue-500',
            'B'     => 'bg-blue-400',
            'C+'    => 'bg-yellow-500',
            'C'     => 'bg-yellow-400',
            'D'     => 'bg-orange-400',
            default => 'bg-red-500',
        };
    }
}
