<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	use Uuids;

	public $incrementing = false;
	
    protected $fillable = ['desc', 'debit', 'credit', 'balance'];
}
