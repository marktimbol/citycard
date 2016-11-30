<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class SubcategoryTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	return [
    		'id'	=> $item->id,
    		'name'	=> $item->name,
    	];
    }
}