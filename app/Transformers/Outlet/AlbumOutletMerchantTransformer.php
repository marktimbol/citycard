<?php

namespace App\Transformers\Outlet;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class AlbumOutletMerchantTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'name'
    	]);

        if( $this->isRelationshipLoaded($item, 'merchant') ) {            
            $output['merchant'] = AlbumOutletMerchantTransformer::transform($item->merchant);
        }

    	return $output;	
    }
}