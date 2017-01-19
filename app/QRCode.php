<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
	protected $fillable = ['photo'];
	
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
