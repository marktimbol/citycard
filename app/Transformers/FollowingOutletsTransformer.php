<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class FollowingOutletsTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'name', 'email', 'phone', 'address', 'lat' ,'lng', 'has_reservation', 'has_messaging', 'has_menus', 'is_open'
        ]);    

        $output['logo'] = $item->merchant ? $item->merchant->logo : '';
        $output['is_following'] = true;      

        if( $this->isRelationshipLoaded($item, 'categories') ) {
            $output['categories'] = CategoryTransformer::transform($item->categories);
        }                        

        return $output; 
    }
}