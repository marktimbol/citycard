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
            $user = auth()->guard('user')->user();

            $output['uuid'] = $user->uuid;
            $output['api_token'] =$user->api_token;                        
    	}

        // Get user profile
        if( auth()->guard('user_api')->check() ) {
            $user = auth()->guard('user_api')->user();

            $output['uuid'] = $user->uuid;
            $output['api_token'] =$user->api_token;
        }        

        if( $this->isRelationshipLoaded($item, 'photos') ) {
            $output['photo'] = null;
            if( $item->photos->count() > 0 ) {
                $output['photo'] = $item->photos->first()->url;
            }
        }    

        if( $this->isRelationshipLoaded($item, 'qrcode') ) {
            $output['qrcode'] = null;
            if( $item->qrcode()->count() > 0 ) {
                $output['qrcode'] = $item->qrcode->photo;
            }
        }

    	return $output;	
    }
}