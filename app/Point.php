<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use Uuids;

    public $incrementing = false;
    
	protected $fillable = [
        'registration', 'login', 'search', 'invite_friend', 'complete_profile',
        'link_facebook_account', 'link_instagram_account', 'reservation', 'delivery',
        'add_to_wishlish'
    ];
}
