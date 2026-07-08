<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = ['class_id', 'name'];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'section_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'section_id');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }
}
