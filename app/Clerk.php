<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use App\Notifications\Clerk\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Clerk extends Authenticatable
{
	use CanReplyToAMessage, Notifiable, Uuids;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'uuid', 'merchant_id', 'first_name', 'last_name',
	    'display_name', 'email', 'password', 'phone'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	    'password', 'remember_token'
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

	/**
	 * Check whether the clerk is nearby the Outlet
	 /* Clerk's lat & lng
	 */
	public function isInOutletRange(Outlet $outlet, $lat, $lng)
	{
		$distance = config('distance.outlet_range');

		$result = collect(DB::select(
			DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(lat) ) ) ) AS distance FROM outlets WHERE id = '. $outlet->id.' HAVING distance < ' . $distance . ' ORDER BY distance'
			)
		));
		
		return $result->count() > 0  ? true : false;
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
	    $this->notify(new ResetPasswordNotification($token));
	}	
}
