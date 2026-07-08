<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarksheetTemplate extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'name', 'file_path', 'placeholders', 'field_mappings', 'is_default', 'description',
    ];

    protected $casts = [
        'placeholders'  => 'array',
        'field_mappings'=> 'array',
        'is_default'    => 'boolean',
    ];

    public function generatedMarksheets(): HasMany
    {
        return $this->hasMany(GeneratedMarksheet::class, 'template_id');
    }

    /**
     * Set this template as default (unset all others first).
     */
    public function setAsDefault(): void
    {
        self::query()->update(['is_default' => false]);
        $this->update(['is_default' => true]);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
