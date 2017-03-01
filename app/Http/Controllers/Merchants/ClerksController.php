<?php

namespace App\Http\Controllers\Merchants;

use App\Clerk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClerksController extends Controller
{
    public function index()
    {

    }

    public function show(Clerk $clerk)
    {
    	$clerk->load('merchant.outlets', 'outlets');

    	return view('merchants.clerks.show', compact('clerk'));
    }
}
