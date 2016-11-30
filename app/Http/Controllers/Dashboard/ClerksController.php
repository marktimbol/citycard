<?php

namespace App\Http\Controllers\Dashboard;

use App\Clerk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClerksController extends Controller
{
    public function index()
    {
    	$clerks = Clerk::with('merchant')->latest()->get();
    	
    	return view('dashboard.clerks.index', compact('clerks'));
    }
}
