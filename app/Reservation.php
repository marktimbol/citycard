<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['item_id', 'date', 'time', 'quantity', 'note'];

    protected $dates = ['date'];
    
    protected $casts = [
    	'quantity'	=> 'int'
    ];
    
    public function item()
    {
    	return $this->belongsTo(ItemForReservation::class, 'item_id');
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
