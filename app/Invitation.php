<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'user_invitations';

	protected $fillable = ['email'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
