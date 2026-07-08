<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolClass extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'sort_order'];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id')->orderBy('name');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getSectionCountAttribute(): int
    {
        return $this->sections()->count();
    }
}
