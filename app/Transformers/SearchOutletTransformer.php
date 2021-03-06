<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class SearchOutletTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'name', 'email', 'phone', 'address', 'lat' ,'lng', 'has_reservation', 'has_messaging', 'has_menus', 'is_open'
        ]);    

        $output['is_following'] = false;

        if( auth()->guard('user_api')->check() )
        {
            $user = auth()->guard('user_api')->user();
            $user->load('outlets');

            $output['is_following'] = $user->outlets->contains($item->id);
        }

        if( $this->isRelationshipLoaded($item, 'merchant') ) {
            $output['logo'] = $item->merchant->logo;
            $output['merchant'] = MerchantTransformer::transform($item->merchant);
        }      

        return $output; 
    }
}