<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	return [
    		'id'	=> $item->id,
    		'name'	=> $item->name,
    		'logo'	=> $item->logo,
    	];
    }
}