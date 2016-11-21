<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'slug'];

    public function setNameAttribute($name)
    {
    	$this->attributes['name'] = $name;
    	$this->attributes['slug'] = str_slug($name);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'area_outlets', 'area_id', 'outlet_id');
    }

    public function merchants()
	{
		return $this->belongsToMany(Merchant::class, 'area_merchants', 'area_id', 'merchant_id');
	}

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
