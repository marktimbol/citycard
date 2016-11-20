<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class External extends Model
{
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'external_posts', 'external_id', 'post_id');
    }
}
