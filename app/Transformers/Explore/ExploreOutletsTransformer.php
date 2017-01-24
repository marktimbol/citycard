<?php

namespace App\Transformers\Explore;

use App\Transformers\MerchantTransformer;
use App\Transformers\Explore\OutletPostTransformer;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ExploreOutletsTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'name'
        ]);    

        if( $this->isRelationshipLoaded($item, 'posts') ) {
            $output['posts_count'] = $item->posts->count();
            $output['posts'] = OutletPostTransformer::transform($item->posts);
        }

        if( $this->isRelationshipLoaded($item, 'merchant') ) {
            $output['merchant'] = MerchantTransformer::transform($item->merchant);
        }   

        return $output; 
    }
}