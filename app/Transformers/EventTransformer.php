<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Themsaid\Transformers\AbstractTransformer;

class EventTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'type', 'title', 'desc', 'isExternal', 'event_date', 'event_time', 'event_location', 'created_at', 'updated_at'
    	]);

        $output['is_favourited'] = false;
        
        if( auth()->guard('user_api')->check() )
        {
            $user = auth()->guard('user_api')->user();
            $user = $user->load('favourites');
            
            $output['is_favourited'] = $user->favourites->contains($item->id);
        }

    	if( $this->isRelationshipLoaded($item, 'category') ) {
    		$output['category'] = CategoryTransformer::transform($item->category);
    	}

    	if( $this->isRelationshipLoaded($item, 'outlets') ) {
    		$output['outlets'] = OutletTransformer::transform($item->outlets);
    	}    	

    	if( $this->isRelationshipLoaded($item, 'merchant') ) {
    		$output['merchant'] = MerchantTransformer::transform($item->merchant);
    	}    	 

    	if( $this->isRelationshipLoaded($item, 'photos') ) {
    		$output['photos'] = PhotosTransformer::transform($item->photos);
    	}   

    	if( $this->isRelationshipLoaded($item, 'sources') ) {
    		$output['source'] = SourceTransformer::transform($item->sources);
    	}   

    	return $output;
    }
}