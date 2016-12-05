<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $merchant)
    {
    	$output = array_only($merchant->toArray(), [
    		'id', 'name', 'logo'
    	]);

    	if( $this->isRelationshipLoaded($merchant, 'outlets') )  {            
    		$output['outlets'] = OutletTransformer::transform($merchant->outlets);
    	}         	

    	return $output;
    }
}