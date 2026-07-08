<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneratedMarksheet extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'student_id', 'exam_id', 'template_id', 'file_path', 'file_type', 'generated_at', 'user_id'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(MarksheetTemplate::class, 'template_id');
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('marksheets.download', $this->id);
    }
}
