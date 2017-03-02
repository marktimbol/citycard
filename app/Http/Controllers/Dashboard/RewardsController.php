<?php

namespace App\Http\Controllers\Dashboard;

use App\Reward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RewardsController extends Controller
{
    public function index()
    {
    	$rewards = Reward::with('outlets:id,name')->latest()->get();

    	return view('dashboard.rewards.index', compact('rewards'));
    }

    public function store(Request $request)
    {
        try {        
        	$reward = Reward::create($request->all());

        	$outlets = collect(explode(',', $request->outlets));
        	$reward->outlets()->sync($outlets);
        	
        	return back();
            
        } catch (Exception $e) {
            
        }
    }
}
