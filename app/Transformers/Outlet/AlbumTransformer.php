<?php

namespace App\Transformers\Outlet;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class AlbumTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'title'
    	]);

        if( $this->isRelationshipLoaded($item, 'photos') ) {            
            if( $item->photos->count() > 0 ) {
                $output['photos_count'] = $item->photos()->count();
                $output['photos'] = $item->photos;
            }
        }

    	return $output;	
    }
}