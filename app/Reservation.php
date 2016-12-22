<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['item_id', 'date', 'time', 'quantity', 'note'];

    public function item()
    {
    	return $this->belongsTo(ItemForReservation::class, 'item_id');
    }
}
