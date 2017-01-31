<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutletSubcategory extends Model
{
	protected $fillable = ['outlet_id', 'subcategory_id'];
	
    protected $table = 'outlet_subcategories';
}
