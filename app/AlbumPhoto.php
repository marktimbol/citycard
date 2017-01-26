<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlbumPhoto extends Model
{
	protected $fillable = ['url'];

	protected $table = 'album_photos';
}
