<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $table = 'order_details';

    protected $fillable = ['product_id', 'name', 'price', 'quantity'];
}
