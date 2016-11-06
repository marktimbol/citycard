<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['type', 'title', 'slug', 'description', 'link', 'photo'];
    
    public function setTitleAttribute($title)
    {
    	$this->attributes['title'] = $title;
    	$this->attributes['slug'] = str_slug($title);
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'outlet_posts', 'post_id', 'outlet_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }
}
