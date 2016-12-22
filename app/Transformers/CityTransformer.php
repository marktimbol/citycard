<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class CityTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'posts_count'
    	]);

    	if( $this->isRelationshipLoaded($item, 'areas') ) {
    		$output['areas'] = AreaTransformer::transform($item->areas);
    	}    

    	return $output;    	
    }
}