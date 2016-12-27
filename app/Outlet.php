<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Outlet extends Authenticatable
{
	use Notifiable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'name', 'email', 'password', 'phone', 'currency', 'address1', 'address2', 'latitude', 'longitude',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token', 'api_token'
	];

	protected $casts = [
		'has_reservation'	=> 'int',
		'has_messaging'	=> 'int',
		'has_menus'	=> 'int',
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

	public function promos()
	{
		return $this->belongsToMany(Promo::class, 'outlet_promos', 'outlet_id', 'promo_id');
	}

	public function clerks()
	{
		return $this->belongsToMany(Clerk::class, 'outlet_clerks', 'outlet_id', 'clerk_id');
	}

	public function posts()
	{
		return $this->belongsToMany(Post::class, 'outlet_posts', 'outlet_id', 'post_id');
	}

	public function photos()
	{
	    return $this->morphMany(Photo::class, 'imageable');
	}

	public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_outlets', 'outlet_id', 'area_id');
    }

    public function reservations()
    {
    	return $this->belongsToMany(Reservation::class, 'outlet_reservations', 'outlet_id', 'reservation_id');
    }

    public function itemsForReservation()
    {
    	return $this->hasMany(ItemForReservation::class);
    }

	public function getLocation()
	{
		if( count($this->areas) > 0 ) {
			return sprintf('%s - %s, %s', $this->areas[0]->name, $this->areas[0]->city->name, $this->areas[0]->city->country->name);
		}
		return '';
	}
}
