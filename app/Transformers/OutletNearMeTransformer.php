<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;
use App\Transformers\Reward\RewardsTransformer;

class OutletNearMeTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'lat' ,'lng', 'is_open'
        ]);

        $output['name'] = $item->merchant->name;

        if( $this->isRelationshipLoaded($item, 'merchant') ) {
            $output['logo'] = $item->merchant->logo != null ? $item->merchant->logo : '';
        }

        if( $this->isRelationshipLoaded($item, 'categories') ) {
            $output['categories'] = CategoryTransformer::transform($item->categories);
        }   

        if( $this->isRelationshipLoaded($item, 'rewards') ) {
            $output['rewards'] = RewardsTransformer::transform($item->rewards);
        }                                

        return $output; 
    }
}