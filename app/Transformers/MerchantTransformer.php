<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'logo'
    	]);

    	if( $this->isRelationshipLoaded($item, 'outlets') )  {       
    		$output['outlets'] = OutletTransformer::transform($item->outlets);
    	}         	

    	return $output;
    }
}