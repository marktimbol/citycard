<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'slug', 'iso_code'];

    public function setNameAttribute($name)
    {
    	$this->attributes['name'] = $name;
    	$this->attributes['slug'] = str_slug($name);
    }

    public function cities()
    {
    	return $this->hasMany(City::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'country_posts', 'country_id', 'post_id');
    }
}
