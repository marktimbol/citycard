<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class CategoryTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name'
    	]);

    	if( $this->isRelationshipLoaded($item, 'subcategories') ) {
    		$output['subcategories'] = SubcategoryTransformer::transform($item->subcategories);
    	}    

    	return $output;
    }
}