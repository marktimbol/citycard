<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['item_id', 'date', 'time', 'flexible_dates', 'quantity', 'option', 'note'];

    protected $dates = ['date'];
    
    protected $casts = [
    	'quantity'	=> 'int',
        'flexible_dates'    => 'bool',
        'confirmed' => 'bool'
    ];
    
    public function item()
    {
    	return $this->belongsTo(ItemForReservation::class, 'item_id');
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_reservations', 'reservation_id', 'outlet_id');
    }    

    public function scopeConfirmed($query)
    {
        return $query->where('confirmed', true);
    }

    public function scopePending($query)
    {
        return $query->where('confirmed', false);
    }    
}
