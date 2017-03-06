<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;
use App\Transformers\Reward\RewardsTransformer;

class UserVouchersTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'verification_code', 'redeemed'
        ]);    

        if( $this->isRelationshipLoaded($item, 'reward') ) {
            $item->reward->load('photos');
            $output['reward'] = RewardsTransformer::transform($item->reward);
        }  

        return $output; 
    }
}