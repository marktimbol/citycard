<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class AreaTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	return $item->name;
    	// return [
    	// 	'id'	=> $item->id,
    	// 	'name'	=> $item->name,
    	// ];
    }
}