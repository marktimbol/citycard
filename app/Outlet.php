<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Outlet extends Authenticatable
{
	use Notifiable, Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	    'name', 'email', 'password', 'phone', 'currency', 'address', 'lat', 'lng'
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
		'is_open'	=> 'int',
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = [];
        $array = $this->toArray();

        $array = [
            'id'    => $this->id,
            'name' => $this->name,
            'address'	=> $this->address,
        ];

        return $array;
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

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'outlet_categories', 'outlet_id', 'category_id');
	}

	public function subcategories()
	{
		return $this->belongsToMany(Subcategory::class, 'outlet_subcategories', 'outlet_id', 'subcategory_id');
	}

    public function reservations()
    {
    	return $this->belongsToMany(Reservation::class, 'outlet_reservations', 'outlet_id', 'reservation_id');
    }

    public function cancelledReservations()
    {
    	return $this->belongsToMany(Reservation::class, 'cancelled_reservations', 'outlet_id', 'reservation_id');
    }

    public function itemsForReservation()
    {
    	return $this->hasMany(ItemForReservation::class);
    }

    public function albums()
    {
    	return $this->hasMany(Album::class);
    }    

    public function menus()
    {
    	return $this->hasMany(OutletMenu::class);
    }

    public function shop_fronts()
    {
    	return $this->hasMany(ShopFront::class);
    }    

	public function getLocation()
	{
		if( count($this->areas) > 0 ) {
			return sprintf('%s - %s, %s', $this->areas[0]->name, $this->areas[0]->city->name, $this->areas[0]->city->country->name);
		}
		return '';
	}

	/**
	 * Near me outlets
	 */
	public function scopeNearMe($query, $lat, $lng, $distance)
	{		
		return collect(DB::select(
			DB::raw('SELECT id, ( 3959 * acos( cos( radians(' . $lat . ') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(' . $lng . ') ) + sin( radians(' . $lat .') ) * sin( radians(lat) ) ) ) AS distance FROM outlets HAVING distance < ' . $distance . ' ORDER BY distance'
			)
		));
	}

}
