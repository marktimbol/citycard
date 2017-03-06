<?php

namespace App\Transformers\Reward;

use Illuminate\Support\Collection;
use App\Transformers\PhotoTransformer;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class RewardsTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'title', 'quantity', 'required_points', 'desc'
    	]);

        if( $this->isRelationshipLoaded($item, 'photos') ) {
            $output['photos'] = PhotoTransformer::transform($item->photos);
        }

        if( $this->isRelationshipLoaded($item, 'outlets') ) {
            $output['outlets'] = RewardOutletsTransformer::transform($item->outlets);
        }

    	return $output;
    }
}