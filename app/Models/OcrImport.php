<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OcrImport extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'user_id', 'image_path', 'extracted_data', 'status', 'error_message', 'language',
    ];

    protected $casts = [
        'extracted_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isProcessed(): bool { return $this->status === 'processed'; }
    public function isFailed(): bool    { return $this->status === 'failed'; }
}
