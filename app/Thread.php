<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = ['clerk_id', 'body'];

    public function replies()
    {
    	return $this->hasMany(Reply::class);
    }
}
