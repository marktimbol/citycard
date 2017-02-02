<?php

namespace App;

use App\Transformers\OutletTransformer;
use App\Transformers\PostTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable, Filterable, PostRelationships;

    protected $fillable = [
        'merchant_id', 'category_id', 'type', 'event_date', 'event_time', 'event_location', 'title', 'desc', 'isExternal', 'published'
    ];

    protected $dates = ['event_date'];

    protected $casts = [
        'isExternal'    => 'boolean',
        'published' => 'int'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($post) {
            if( auth()->check() ) {            
                if( auth()->user()->hasRole('admin') ) {
                    $post->published = true;
                }
            }
        });
    }
    
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

    public function scopeUpcomingEvents($query)
    {
        return $query->where('type', 'events')
            ->where('event_date', '>=', Carbon::now());
    }

    public function isPublished()
    {
        return $this->published == 1;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = [];

        if( $this->isPublished() )
        {            
            $array = $this->toArray();
            $array = [
                'id'    => $this->id,
                'title' => $this->title,
                'outlet' => OutletTransformer::transform($this->outlets)
            ];
        } else {
            $this->unsearchable();
        }

        return $array;
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
