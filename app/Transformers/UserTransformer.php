<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'email', 'mobile',
    	]);

    	if( auth()->guard('user')->check() ) {
			$output['api_token'] = auth()->guard('user')->user()->api_token;
    	}

        // Get user profile
        if( auth()->guard('user_api')->check() ) {
            $output['api_token'] = auth()->guard('user_api')->user()->api_token;
        }        

        if( $this->isRelationshipLoaded($item, 'photos') ) {
            $output['photo'] = $item->photos->first()->url;
        }    

        if( $this->isRelationshipLoaded($item, 'qrcode') ) {
            $output['qrcode'] = $item->qrcode->photo;
        }                

    	return $output;	
    }
}