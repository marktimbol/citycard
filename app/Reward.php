<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use Uuids;

    public $incrementing = false;
    
    protected $fillable = ['merchant_id', 'title', 'quantity', 'required_points', 'desc'];

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['slug'] = str_slug($title);
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'outlet_rewards', 'reward_id', 'outlet_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function photos()
    {
        return $this->hasMany(RewardPhotos::class);
    }
}
