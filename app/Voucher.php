<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['reward_id', 'verification_code', 'redeemed'];

    public $incrementing = false;

    protected $casts = [
        'redeemed'  => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid1()->toString();
        });
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    // Well, this should only return 1 result
    public function owners()
    {
        return $this->belongsToMany(User::class, 'user_vouchers', 'voucher_id', 'user_id');
    }
}
