<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Uuids;

    public $incrementing = false;

    protected $fillable = [
    	'outlet_id', 'amount', 'address', 'city', 'country', 'phone', 'email'
    ];

    public function details()
    {
    	return $this->hasMany(OrderDetails::class);
    }
}
