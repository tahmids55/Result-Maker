<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'name', 'gender', 'roll', 'registration_no', 'father_name', 'mother_name',
        'class_id', 'section_id', 'session', 'profile_photo', 'dob', 'phone', 'address',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function generatedMarksheets(): HasMany
    {
        return $this->hasMany(GeneratedMarksheet::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return null;
    }

    public function getResultForExam(int $examId): ?Result
    {
        return $this->results()->where('exam_id', $examId)->first();
    }
}
