<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Themsaid\Transformers\AbstractTransformer;

class PostTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'type', 'title', 'desc', 'isExternal', 'created_at', 'updated_at'
    	]);

        if( $item->type == 'events' )
        {
            $output['event_date'] = ! empty($item->event_date) ? $item->event_date->toDateTimeString() : null;
            $output['event_time'] = $item->event_time;
        }

        $post = $item->load('outlets.itemsForReservation');

        $forReservation = false;
        foreach( $post->outlets as $outlet ) {
            if( $outlet->has_reservation ) {
                $forReservation = $outlet->itemsForReservation->contains('title', $item->title);
            }
        }

        $output['for_reservation'] = $forReservation;

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