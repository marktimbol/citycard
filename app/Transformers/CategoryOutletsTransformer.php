<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class CategoryOutletsTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'name', 'address', 'is_open'
        ]);    

        $output['logo'] = $item->merchant ? $item->merchant->logo : '';
        
        if( auth()->guard('user_api')->check() )
        {
            $user = auth()->guard('user_api')->user();
            $user = $user->load('outlets');
            $output['is_following'] = $user->outlets->contains($item->id) ? true : false;
        }

        $output['posts_count'] = $item->posts->count();

        return $output; 
    }
}