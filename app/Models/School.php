<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use \App\Traits\BelongsToUser;

    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'address',
        'phone',
        'email',
        'footer_text',
        'signature',
        'date_format',
        'gpa_scale',
        'sms_api_key',
        'whatsapp_api_key',
    ];

    /**
     * Get the singleton school record.
     */
    public static function getSettings(): ?self
    {
        return self::first();
    }

    /**
     * Get logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Get signature URL.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if ($this->signature) {
            return asset('storage/' . $this->signature);
        }
        return null;
    }
}
