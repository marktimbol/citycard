<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Notifications\Notifiable;
use App\Transformers\UserOutletTransformer;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'name', 'email', 'mobile', 'dob', 'gender', 'marital_status',
        'profession', 'password', 'verification_code', 'points'
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
        return $this->belongsToMany(Outlet::class, 'outlet_followers', 'user_id', 'outlet_id')
            ->withTimestamps();
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

    public function following_outlets()
    {
        return UserOutletTransformer::transform($this->outlets);
    }

    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'user_rewards', 'user_id', 'reward_id');
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'user_vouchers', 'user_id', 'voucher_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }    

    public function getAvailablePoints()
    {
        $transaction = $this->transactions()->latest()->first();
        return count($transaction) > 0 ? $transaction->balance : 0;  
    }

    public function givePoints($points, $description)
    {
        return $this->transactions()->create([
            'desc'   => $description,
            'credit'    => $points,
            'debit' => 0,
            'balance'   =>  $this->getAvailablePoints() + $points
        ]);          
    }

    public function takePoints($points, $description)
    {
        return $this->transactions()->create([
            'desc'   => $description,
            'credit'    => 0,
            'debit' => $points,
            'balance'   =>  $this->getAvailablePoints() - $points
        ]);          
    }

    public function alreadyInvited($email)
    {
        return $this->invitations()->where('email', $email)->count() > 0;      
    }
}
