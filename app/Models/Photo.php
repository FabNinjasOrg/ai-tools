<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model
{
    protected $fillable = [
        'event_id', 'filename', 'path', 'size_bytes', 'embedding_json',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
