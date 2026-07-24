<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'username', 'role', 'admin_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // ── Role Helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Get the effective owner ID for data scoping.
     * If this user is a teacher, return their admin's ID.
     * If this user is an admin, return their own ID.
     */
    public function getOwnerIdAttribute(): int
    {
        return $this->admin_id ?? $this->id;
    }

    // ── Relationships ───────────────────────────────────────────────

    /**
     * The admin who created this teacher.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Teachers created by this admin.
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(User::class, 'admin_id');
    }

    /**
     * Subjects assigned to this teacher.
     */
    public function assignedSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher')->withTimestamps();
    }

    public function ocrImports()
    {
        return $this->hasMany(OcrImport::class);
    }
}
