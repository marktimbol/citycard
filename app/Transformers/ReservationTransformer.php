<?php

namespace App\Transformers;

use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;
use App\Transformers\ItemsForReservationTransformer;

class ReservationTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
            'id', 'date', 'time', 'quantity', 'note', 'confirmed'
    	]);

    	if( $this->isRelationshipLoaded($item, 'item') ) {
    		$output['item'] = ItemsForReservationTransformer::transform($item->item);
    	}   

        if( $this->isRelationshipLoaded($item, 'user') ) {
            $output['user'] = UserTransformer::transform($item->user);
        }            

    	return $output;
    }
}