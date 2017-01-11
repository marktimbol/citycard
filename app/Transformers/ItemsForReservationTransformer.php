<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ItemsForReservationTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        return array_only($item->toArray(), [
            'id', 'title', 'options'
        ]);    
    }
}