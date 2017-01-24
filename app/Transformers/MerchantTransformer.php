<?php

namespace App\Transformers;

use App\Transformers\SubcategoryTransformer;
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

        if( $this->isRelationshipLoaded($item, 'subcategories') )  {       
            $output['subcategories'] = SubcategoryTransformer::transform($item->subcategories);
        }                 	

    	return $output;
    }
}