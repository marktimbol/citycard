<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class OutletTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'name', 'email', 'phone', 'address1', 'address2', 'latitude' ,'longitude', 'has_reservation', 'has_messaging', 'has_menus', 'is_open'
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
            if( auth()->guard('clerk_api')->check() ) {
                $reservations = $item->reservations->partition(function($reservation) {
                    return $reservation->confirmed == true;
                });
                $output['reservations_count'] = $item->reservations->count();
                $output['confirmed_reservations_count'] = $reservations[0]->count();
                $output['pending_reservations_count'] = $reservations[1]->count();
            } else {
                $output['reservations'] = ReservationTransformer::transform($item->reservations);
            }            
        }

        // Change these soon.
        if( $this->isRelationshipLoaded($item, 'photos') ) {
            if( auth()->guard('clerk_api')->check() ) {
                $output['photos_count'] = $item->photos->count();
                $output['menus_count'] = 0;
                $output['albums_count'] = 0;
            }
        }

        if( auth()->guard('clerk_api')->check() ) {
            $output['messages_count'] = 25;
            $output['read_messages_count'] = 20;
            $output['unread_messages_count'] = 5;
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