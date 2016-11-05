<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Clerk extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'merchant_id', 'first_name', 'last_name', 
	    'email', 'password', 'phone', 'country', 'city'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token', 'api_token',
	];

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = $email;
        $this->attributes['api_token'] = str_random(60);
    }
	
	public function merchant()
	{
		return $this->belongsTo(Merchant::class);
	}

	public function outlets()
	{
		return $this->belongsToMany(Outlet::class, 'outlet_clerks', 'outlet_id', 'clerk_id');
	}

}
