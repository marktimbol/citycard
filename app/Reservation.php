<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['item_id', 'date', 'time', 'flexible_dates', 'quantity', 'option', 'note'];

    protected $dates = ['date'];
    
    protected $casts = [
    	'quantity'	=> 'int',
        'flexible_dates'    => 'bool'
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
