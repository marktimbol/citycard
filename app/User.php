<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use CanSendMessage, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'mobile', 'password', 'verification_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = $email;
        $this->attributes['api_token'] = str_random(60);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($user) {
            $user->token = str_random(30);
        });
    }

    public function favourites()
    {
        return $this->belongsToMany(Post::class, 'user_favourites', 'user_id', 'post_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_followers', 'user_id', 'merchant_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_followers', 'user_id', 'outlet_id');
    }    
}
