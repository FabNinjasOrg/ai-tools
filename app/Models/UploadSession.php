<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploadSession extends Model
{
    use SoftDeletes;

    protected $table = 'upload_sessions';

    protected $fillable = [
        'batch_id',
        'user_id',
        'album_id',
        'event_id',
        'status',
    ];
}
