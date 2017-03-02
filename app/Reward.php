<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = ['merchant_id', 'title', 'quantity', 'required_points', 'desc'];

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid1()->toString();
        });
    }

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['slug'] = sprintf('%s-%s', str_slug($title), $this->id);
    }

    public function outlets()
    {
    	return $this->belongsToMany(Outlet::class, 'outlet_rewards', 'reward_id', 'outlet_id');
    }
}
