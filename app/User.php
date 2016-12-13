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
        'name', 'email', 'mobile', 'password',
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
}
