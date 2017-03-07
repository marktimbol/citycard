<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use Uuids;

    public $incrementing = false;
    
    protected $fillable = ['reward_id', 'verification_code', 'redeemed'];

    protected $casts = [
        'redeemed'  => 'boolean'
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_vouchers', 'voucher_id', 'outlet_id');
    }

    // Well, this should only return 1 result
    public function owners()
    {
        return $this->belongsToMany(User::class, 'user_vouchers', 'voucher_id', 'user_id');
    }
}
