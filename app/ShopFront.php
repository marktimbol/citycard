<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopFront extends Model
{
    protected $fillable = ['url'];

    protected $table = 'shop_fronts';
    
}
