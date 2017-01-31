<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class OutletNearMeTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'phone', 'lat' ,'lng', 'is_open'
        ]);

        $output['name'] = $item->merchant->name;
        
        if( $this->isRelationshipLoaded($item, 'merchant') ) {
            $output['logo'] = $item->merchant->logo != null ? $item->merchant->logo : '';
        }

        if( $this->isRelationshipLoaded($item, 'categories') ) {
            $output['categories'] = CategoryTransformer::transform($item->categories);
        }                        

        return $output; 
    }
}