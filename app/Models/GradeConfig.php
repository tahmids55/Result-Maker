<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeConfig extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'grade', 'gpa', 'min_percentage', 'max_percentage', 'label', 'sort_order',
    ];

    protected $casts = [
        'gpa'            => 'float',
        'min_percentage' => 'float',
        'max_percentage' => 'float',
    ];

    /**
     * Resolve grade/GPA from a percentage.
     */
    public static function resolve(float $percentage): array
    {
        $config = self::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderByDesc('min_percentage')
            ->first();

        if ($config) {
            return ['grade' => $config->grade, 'gpa' => $config->gpa];
        }

        return ['grade' => 'F', 'gpa' => 0.00];
    }
}
