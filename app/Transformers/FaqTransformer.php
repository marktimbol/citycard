<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Themsaid\Transformers\AbstractTransformer;

class FaqTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'question', 'answer'
    	]);
        
    	return $output;
    }
}