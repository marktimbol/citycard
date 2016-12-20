<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class MerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output['id'] = $item->merchant->id;
        $output['name'] = $item->merchant->name;
        $output['logo'] = $item->merchant->logo;

    	if( $this->isRelationshipLoaded($item->merchant, 'outlets') )  {       
            $merchantOutlets = $item->merchant->outlets->reject(function($outlet) use($item) {
                return $outlet->id == $item->id;
            });
    		$output['outlets'] = OutletTransformer::transform($merchantOutlets);
    	}         	

    	return $output;
    }
}