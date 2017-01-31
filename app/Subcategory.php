<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $fillable = ['category_id', 'name'];

    protected $casts = [
    	'category_id'	=> 'int'
   ];
   
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'subcategory_posts', 'subcategory_id', 'post_id');
    }
}
