<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'user_id', 'uuid', 'name', 'zip_filename', 'zip_path', 'zip_size_bytes', 'photos_count', 'public_url', 'uploader_url', 'upload_status',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function otpAttempts(): HasMany
    {
        return $this->hasMany(OtpVerificationAttempt::class);
    }

    public function latestPhotos(): HasMany
    {
        return $this->hasMany(Photo::class)->latest();
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
