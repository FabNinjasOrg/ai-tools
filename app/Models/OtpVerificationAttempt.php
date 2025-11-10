<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpVerificationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'album_uuid',
        'phone_number',
        'ip_address',
        'user_agent',
        'attempts',
        'matched_found_photos',
        'session_token',
        'matched_photo_id_json',
        'zip_s3_url',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}


