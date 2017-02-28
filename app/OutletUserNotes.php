<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutletUserNotes extends Model
{
    protected $fillable = ['outlet_id', 'user_id', 'notes'];

    protected $table = 'outlet_user_notes';
}
