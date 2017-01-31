<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantSubcategory extends Model
{
	protected $fillable = ['merchant_id', 'subcategory_id'];
	
    protected $table = 'merchant_subcategories';
}
