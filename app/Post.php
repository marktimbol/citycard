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
        return $this->belongsToMany(Source::class, 'source_posts', 'post_id', 'source_id')
                    ->withPivot('link');
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

    public static function byCity($city)
    {
        $posts = Post::latest()->get();
        $posts->load('outlets.areas.city', 'photos', 'sources');

        return $posts->filter(function($post, $key) use ($city) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    return $city->id == $area->city->id;
                }
            }
        })->all();
    }

    public static function byArea($selectedArea)
    {
        $posts = Post::has('outlets')->latest()->get();
        $posts->load('outlets.areas', 'photos', 'sources');

        return $posts->filter(function($post, $key) use ($selectedArea) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    return $selectedArea->id == $area->id;
                }
            }
        })->all();
    }

    public static function byAreas($area_ids)
    {
        $posts = Post::has('outlets')->latest()->get();
        $posts->load('outlets.areas', 'photos', 'sources');

        return $posts->filter(function($post, $key) use ($area_ids) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    return in_array($area->id, $area_ids);
                }
            }
        })->all();
    }
}
