<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class OutletTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'email', 'phone', 'address1', 'address2', 'latitude' ,'longitude'
    	]);    

        if( $this->isRelationshipLoaded($item, 'areas') ) {
            $output['areas'] = AreaTransformer::transform($item->areas);
        }     

    	if( $this->isRelationshipLoaded($item, 'merchant') ) {
    		$output['merchant'] = MerchantTransformer::transform($item->merchant);
    	}      

    	if( $this->isRelationshipLoaded($item, 'clerks') ) {
    		$output['clerks'] = ClerkTransformer::transform($item->clerks);
    	}      

    	if( $this->isRelationshipLoaded($item, 'posts') ) {
            $output['posts_count'] = $item->posts->count();
    		$output['posts'] = PostTransformer::transform($item->posts);
    	}     

    	return $output;	
    }
}