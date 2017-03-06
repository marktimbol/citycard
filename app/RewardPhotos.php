<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class RewardPhotos extends Model
{
    protected $table = 'reward_photos';

    protected $fillable = ['url', 'thumbnail'];

    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid1()->toString();
        });
    }    
}
