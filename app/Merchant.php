<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Merchant extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'name', 'phone', 'country', 'city', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token',
	];

	protected $with = ['outlets', 'posts'];
	
	public function setPasswordAttribute($password)
	{
		$this->attributes['password'] = bcrypt($password);
	}

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['api_token'] = str_random(60);
    }

	public function outlets()
	{
		return $this->hasMany(Outlet::class);
	}

	public function clerks()
	{
		return $this->hasMany(Clerk::class);
	}

	public function promos()
	{
		return $this->hasMany(Promo::class);
	}

	public function posts()
	{
		return $this->hasMany(Post::class);
	}

	public static function createPost($attributes)
	{
		return Auth::guard('merchant')
			->user()
			->posts()
			->create($attributes);
	}
}
