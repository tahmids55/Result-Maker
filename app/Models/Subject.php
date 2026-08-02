<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use \App\Traits\BelongsToUser;
    use \App\Traits\LogsActivityTrait;

    use HasFactory;

    protected $fillable = [
        'name', 'code', 'class_id', 'section_id',
        'exam_components', 'is_optional', 'sort_order', 'has_sub_subjects', 
        'full_marks', 'pass_marks', 'is_individual_pass',
    ];

    protected $casts = [
        'exam_components' => 'array',
        'is_optional'     => 'boolean',
        'has_sub_subjects' => 'boolean',
        'is_individual_pass' => 'boolean',
        'full_marks'      => 'float',
        'pass_marks'      => 'float',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subSubjects(): HasMany
    {
        return $this->hasMany(SubSubject::class)->orderBy('sort_order');
    }

    public function marks(): HasMany
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Teachers assigned to this subject.
     */
    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teacher')->withTimestamps();
    }

    /**
     * Total full marks across all components.
     */
    public function getTotalFullMarksAttribute(): float
    {
        return (float) $this->full_marks;
    }

    /**
     * Total pass marks across all components.
     */
    public function getTotalPassMarksAttribute(): float
    {
        return (float) $this->pass_marks;
    }

    /**
     * Ordered component names for spreadsheet columns.
     */
    public function getComponentNamesAttribute(): array
    {
        return array_keys($this->exam_components ?? []);
    }
}
