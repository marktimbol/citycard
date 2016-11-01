<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
	protected $fillable = ['title'];

	// protected $with = ['outlets'];
	
	public function outlets()
	{
		return $this->belongsToMany(Outlet::class, 'outlet_promos', 'promo_id', 'outlet_id');
	}
}
