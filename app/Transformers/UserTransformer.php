<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'email', 'mobile',
    	]);

    	if( auth()->guard('user')->check() ) {
			$output['api_token'] = auth()->guard('user')->user()->api_token;
    	}

    	return $output;	
    }
}