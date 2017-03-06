<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['description', 'debit', 'credit', 'balance'];

    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Uuid::uuid1()->toString();
        });
    }   
}
