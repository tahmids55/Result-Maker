<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'name', 'code', 'class_id', 'section_id',
        'exam_components', 'is_optional', 'sort_order', 'has_sub_subjects',
    ];

    protected $casts = [
        'exam_components' => 'array',
        'is_optional'     => 'boolean',
        'has_sub_subjects' => 'boolean',
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
     * Total full marks across all components.
     */
    public function getTotalFullMarksAttribute(): float
    {
        if ($this->has_sub_subjects) {
            return $this->subSubjects->sum(function($sub) {
                return collect($sub->exam_components)->sum(fn($c) => $c['full'] ?? 0);
            });
        }
        return collect($this->exam_components)->sum(fn($c) => $c['full'] ?? 0);
    }

    /**
     * Total pass marks across all components.
     */
    public function getTotalPassMarksAttribute(): float
    {
        if ($this->has_sub_subjects) {
            return $this->subSubjects->sum(function($sub) {
                return collect($sub->exam_components)->sum(fn($c) => $c['pass'] ?? 0);
            });
        }
        return collect($this->exam_components)->sum(fn($c) => $c['pass'] ?? 0);
    }

    /**
     * Ordered component names for spreadsheet columns.
     */
    public function getComponentNamesAttribute(): array
    {
        return array_keys($this->exam_components ?? []);
    }
}
