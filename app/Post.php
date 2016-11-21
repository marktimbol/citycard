<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'merchant_id', 'type', 'title', 'desc', 'isExternal'
    ];

    public function setTitleAttribute($title)
    {
    	$this->attributes['title'] = $title;
    	$this->attributes['slug'] = str_slug($title);
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'outlet_posts', 'post_id', 'outlet_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'source_posts', 'post_id', 'source_id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
    }

    public static function getOffers()
    {
        return static::with('outlets', 'photos')
            ->where('type', 'offer')
            ->get();
    }

    public static function getEvents()
    {
        return static::with('outlets', 'photos')
            ->where('type', 'ticket')
            ->get();
    }
}
