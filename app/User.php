<?php

namespace App;

use Ramsey\Uuid\Uuid;
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
        'uuid', 'name', 'email', 'mobile',
        'dob', 'gender', 'marital_status',
        'profession', 'password', 'verification_code',
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
            $user->uuid = Uuid::uuid1()->toString();
            // Email confirmation token
            $user->token = str_random(30);
            // Mobile verification code
            $user->verification_code = mt_rand(100000,999999);
        });
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable');
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
        return $this->belongsToMany(Outlet::class, 'outlet_followers', 'user_id', 'outlet_id')->withTimestamps();;
    }

    public function follows($outlet)
    {
        $this->outlets()->attach($outlet);
    }

    public function unfollow($outlet)
    {
        $this->outlets()->detach($outlet);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }    

    public function qrcode()
    {
        return $this->hasOne(QRCode::class);
    }
}
