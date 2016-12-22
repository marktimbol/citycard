<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class CountryTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'iso_code', 'posts_count'
    	]);

    	if( $this->isRelationshipLoaded($item, 'cities') ) {
    		$output['cities'] = CityTransformer::transform($item->cities);
    	}    

    	return $output;
    }
}