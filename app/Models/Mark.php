<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mark extends Model
{
    use \App\Traits\BelongsToUser;
    use \App\Traits\LogsActivityTrait;

    use HasFactory;

    protected $fillable = [
        'student_id', 'subject_id', 'sub_subject_id', 'exam_id',
        'component', 'obtained_marks', 'full_marks', 'pass_marks', 'is_absent',
    ];

    protected $casts = [
        'obtained_marks' => 'float',
        'full_marks'     => 'float',
        'pass_marks'     => 'float',
        'is_absent'      => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function subSubject(): BelongsTo
    {
        return $this->belongsTo(SubSubject::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function getIsPassedAttribute(): bool
    {
        if ($this->is_absent) return false;
        return $this->obtained_marks >= $this->pass_marks;
    }

    public function getPercentageAttribute(): float
    {
        if ($this->full_marks == 0) return 0;
        return round(($this->obtained_marks / $this->full_marks) * 100, 2);
    }

    protected function getActivityLabel(): string
    {
        $studentName = $this->student?->name ?? 'Student#' . $this->student_id;
        $subjectName = $this->subSubject?->name ?? $this->subject?->name ?? 'Unknown Subject';
        return "{$studentName} - {$subjectName} ({$this->component})";
    }
}
