<?php

namespace App\Http\Controllers\Api;

use App\Reward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\Reward\RewardsTransformer;

class RewardsController extends Controller
{
    public function index()
    {
    	$rewards = Reward::with('photos', 'outlets')->get();

    	return RewardsTransformer::transform($rewards);
    }

    public function show(Reward $reward)
    {
    	$reward->load('outlets');

    	return RewardsTransformer::transform($reward);
    }
}
