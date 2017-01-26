<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['title'];

    public function photos()
    {
    	return $this->hasMany(AlbumPhoto::class);
    }

    public function outlet()
    {
    	return $this->belongsTo(Outlet::class);
    }
}
