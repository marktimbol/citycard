<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->merchant->toArray(), [
    		'id', 'name', 'logo'
    	]);

    	if( $this->isRelationshipLoaded($item->merchant, 'outlets') )  {       
            $merchantOutlets = $item->merchant->outlets->reject(function($outlet) use($item) {
                return $outlet->id == $item->id;
            });
    		$output['outlets'] = OutletTransformer::transform($merchantOutlets);
    	}         	

    	return $output;
    }
}