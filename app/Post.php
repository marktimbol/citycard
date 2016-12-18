<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Filterable;

    protected $fillable = [
        'merchant_id', 'category_id', 'type', 'event_date', 'event_time', 'title', 'desc', 'isExternal', 'published'
    ];

    protected $dates = ['event_date'];

    public function setTitleAttribute($title)
    {
    	$this->attributes['title'] = $title;
    	$this->attributes['slug'] = str_slug($title);
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeUnpublished($query)
    {
        return $query->where('published', false);
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'outlet_posts', 'post_id', 'outlet_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'subcategory_posts', 'post_id', 'subcategory_id');
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

    public static function getDeals()
    {
        return static::where('type', 'deals');
    }

    public static function getEvents()
    {
        return static::where('type', 'events');
    }
}
