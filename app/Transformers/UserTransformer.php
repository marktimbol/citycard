<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'name', 'email', 'mobile', 'dob', 'gender', 'marital_status', 'profession',
    	]);

        $transaction = $item->transactions()->first();
        $output['points'] = count($transaction) > 0 ? $transaction->balance : 0;

    	if( auth()->guard('user')->check() ) {
            $user = auth()->guard('user')->user();

            $output['uuid'] =$user->uuid;                        
            $output['api_token'] =$user->api_token;                        
    	}

        // Get user profile
        if( auth()->guard('user_api')->check() ) {
            $user = auth()->guard('user_api')->user();
            $output['uuid'] =$user->uuid;
            $output['api_token'] =$user->api_token;
        }

        if( auth()->guard('clerk_api')->check() ) {
            $output['uuid'] = $item->uuid;
        }

        if( $this->isRelationshipLoaded($item, 'photos') ) {
            $output['photo'] = '';
            if( $item->photos->count() > 0 ) {
                $output['photo'] = $item->photos->first()->url;
            }
        }    

        if( $this->isRelationshipLoaded($item, 'qrcode') ) {
            $output['qrcode'] = '';
            if( $item->qrcode()->count() > 0 ) {
                $output['qrcode'] = $item->qrcode->photo;
            }
        }

        if( $this->isRelationshipLoaded($item, 'reservations') ) {
            $output['pending_reservations'] = $item->reservations()->pending()->count();
        }        

    	return $output;	
    }
}