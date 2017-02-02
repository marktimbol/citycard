<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutletMenu extends Model
{
    protected $fillable = ['url'];

    protected $table = 'outlet_menus';
}
