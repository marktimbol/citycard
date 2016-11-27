<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class SourceTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	return [
    		'name'	=> $item->name,
    		'link'	=> $item->pivot->link,
    	];
    }
}