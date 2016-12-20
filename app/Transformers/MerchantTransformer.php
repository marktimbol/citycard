<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        // dd('merchant', $item);

    	$output = array_only($item->toArray(), [
    		'id', 'name', 'logo'
    	]);

    	if( $this->isRelationshipLoaded($item, 'outlets') )  {       
            // $merchantOutlets = $item->merchant->outlets->reject(function($outlet) use($item) {
            //     return $outlet->id == $item->id;
            // });
    		$output['outlets'] = OutletTransformer::transform($item->outlets);
    	}         	

    	return $output;
    }
}