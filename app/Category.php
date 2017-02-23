<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

	public function outlets()
	{
		return $this->belongsToMany(Outlet::class, 'outlet_categories', 'category_id', 'outlet_id');
	}    
}
