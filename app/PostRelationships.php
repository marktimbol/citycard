<?php

namespace App;

trait PostRelationships
{
    public function creator()
    {
        return $this->belongsToMany(Admin::class, 'admin_posts', 'post_id', 'admin_id');
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
}