<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
	use Notifiable, HasRoles;
    

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token',
	];

    public function setNameAttribute($name)
    {
        $this->attributes['name'] = $name;
        $this->attributes['api_token'] = str_random(60);
    }

    public function setPasswordAttribute($password)
    {
    	$this->attributes['password'] = bcrypt($password);
    }

    public function merchants()
    {
    	return $this->belongsToMany(Merchant::class, 'admin_merchants', 'admin_id', 'merchant_id');
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'admin_outlets', 'admin_id', 'outlet_id');
    }   

    public function posts()
    {
    	return $this->belongsToMany(Post::class, 'admin_posts', 'admin_id', 'post_id');
    }   

}
