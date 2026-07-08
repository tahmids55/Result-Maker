<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSubject extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'exam_components',
        'sort_order',
    ];

    protected $casts = [
        'exam_components' => 'array',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
