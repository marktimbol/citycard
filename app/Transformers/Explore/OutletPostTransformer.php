<?php

namespace App\Transformers\Explore;

use App\Transformers\PhotosTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Themsaid\Transformers\AbstractTransformer;

class OutletPostTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'type', 'title', 'slug', 'isExternal', 'created_at', 'updated_at'
    	]);

        if( $item->type == 'events' )
        {
            $output['event_date'] = ! empty($item->event_date) ? $item->event_date->toDateTimeString() : null;
            $output['event_time'] = $item->event_time;
        }

    	if( $this->isRelationshipLoaded($item, 'photos') ) {
    		$output['photos'] = PhotosTransformer::transform($item->photos);
    	}

    	return $output;
    }
}