<?php

namespace App\Http\Controllers;

use App\Merchant;
use Illuminate\Http\Request;

class MechantsController extends Controller
{
    public function show(Merchant $merchant)
    {
    	$merchant->load('posts');
    	return view('public.merchants.show', compact('merchant'));
    }
}
