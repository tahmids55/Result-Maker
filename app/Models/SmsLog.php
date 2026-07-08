<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsLog extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'student_id', 'phone_number', 'message', 'channel',
        'status', 'provider_message_id', 'error_message', 'sent_at', 'scheduled_at',
    ];

    protected $casts = [
        'sent_at'      => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
