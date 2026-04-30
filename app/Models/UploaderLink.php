<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UploaderLink extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'uploader_links';

	protected $fillable = [
		'album_id',
		'url',
		'passcode',
		'start',
		'end',
		'status',
	];
}


