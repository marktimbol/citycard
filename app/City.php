<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name', 'slug'];

    public function setNameAttribute($name)
    {
    	$this->attributes['name'] = $name;
    	$this->attributes['slug'] = str_slug($name);
    }

    public function areas()
    {
    	return $this->hasMany(Area::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function posts()
    {
        // return $this->hasManyThrough(Post::class, City::class);
        return $this->belongsToMany(Post::class, 'city_posts', 'city_id', 'post_id');
    }
}
