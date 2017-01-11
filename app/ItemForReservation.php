<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemForReservation extends Model
{
    protected $fillable = ['outlet_id', 'title', 'options'];

    protected $table = 'item_for_reservations';

    protected $casts = [
    	'options'	=> 'array'
    ];

    public function setOptionsAttribute($options)
    {
    	$this->attributes['options'] = json_encode($options);
    }
}
