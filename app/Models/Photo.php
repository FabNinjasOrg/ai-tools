<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'event_id', 'album_id', 'filename', 'path', 'size_bytes', 'embedding_json',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
