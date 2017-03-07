<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RewardPhotos extends Model
{
    use Uuids;

    protected $table = 'reward_photos';

    public $incrementing = false;
    
    protected $fillable = ['url', 'thumbnail'];
}
