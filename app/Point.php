<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
	protected $fillable = [
        'registration', 'login', 'search', 'invite_friend', 'complete_profile',
        'link_facebook_account', 'link_instagram_account', 'reservation', 'delivery',
        'add_to_wishlish'
    ];
	
	public $incrementing = false;
	
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid1()->toString();
        });
    }   
}
