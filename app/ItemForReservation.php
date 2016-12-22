<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemForReservation extends Model
{
    protected $fillable = ['title'];

    protected $table = 'item_for_reservations';
}
