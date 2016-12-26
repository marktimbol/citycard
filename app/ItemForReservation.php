<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemForReservation extends Model
{
    protected $fillable = ['outlet_id', 'title'];

    protected $table = 'item_for_reservations';
}
