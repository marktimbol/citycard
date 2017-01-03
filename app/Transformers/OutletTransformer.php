<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class OutletTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'email', 'phone', 'address1', 'address2', 'latitude' ,'longitude', 'has_reservation', 'has_messaging', 'has_menus'
    	]);    

        $output['is_following'] = false;

        if( auth()->guard('user_api')->check() )
        {
            $user = auth()->guard('user_api')->user();
            $user->load('outlets');

            $output['is_following'] = $user->outlets->contains($item->id);
        }

        if( $this->isRelationshipLoaded($item, 'posts') ) {
            $output['posts_count'] = $item->posts->count();
            $output['posts'] = PostTransformer::transform($item->posts);
        }  

        if( $this->isRelationshipLoaded($item, 'itemsForReservation') ) {
            $output['reservation_items_count'] = $item->itemsForReservation->count();
            // $output['for_reservations'] = ItemsForReservationTransformer::transform($item->itemsForReservation);
        }                

        if( $this->isRelationshipLoaded($item, 'reservations') ) {
            $output['reservations'] = ReservationTransformer::transform($item->reservations);
        }  

    	if( $this->isRelationshipLoaded($item, 'merchant') ) {
            $output['merchant'] = MerchantTransformer::transform($item->merchant);
    	}      

    	if( $this->isRelationshipLoaded($item, 'clerks') ) {
    		$output['clerks'] = ClerkTransformer::transform($item->clerks);
    	}                

    	return $output;	
    }
}