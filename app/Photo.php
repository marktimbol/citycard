<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
	protected $fillable = ['url'];

    public function imageable()
    {
    	return $this->morphTo();
    }
}
