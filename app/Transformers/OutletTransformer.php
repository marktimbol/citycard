<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class OutletTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name'
    	]);    

    	if( $this->isRelationshipLoaded($item, 'merchant') ) {
    		$output['merchant'] = MerchantTransformer::transform($item->merchant);
    	}      

    	if( $this->isRelationshipLoaded($item, 'clerks') ) {
    		$output['clerks'] = ClerkTransformer::transform($item->clerks);
    	}      

    	if( $this->isRelationshipLoaded($item, 'posts') ) {
    		$output['posts'] = PostTransformer::transform($item->posts);
    	}     


    	return $output;	
    }
}