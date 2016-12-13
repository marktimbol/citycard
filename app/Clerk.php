<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Clerk extends Authenticatable
{
	use CanReplyToAMessage, Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'merchant_id', 'first_name', 'last_name',
	    'email', 'password', 'phone'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token', 'api_token'
	];

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = $email;
        $this->attributes['api_token'] = str_random(60);
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }    

	public function merchant()
	{
		return $this->belongsTo(Merchant::class);
	}

	public function outlets()
	{
		return $this->belongsToMany(Outlet::class, 'outlet_clerks', 'clerk_id', 'outlet_id');
	}

	public function fullName()
	{
		return sprintf('%s %s', $this->first_name, $this->last_name);
	}

	public function assignTo(Outlet $outlet)
	{
		return $this->outlets()->attach($outlet);
	}
}
