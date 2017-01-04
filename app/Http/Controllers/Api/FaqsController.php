<?php

namespace App\Http\Controllers\Api;

use App\Faq;
use App\Http\Controllers\Controller;
use App\Transformers\FaqTransformer;
use Illuminate\Http\Request;

class FaqsController extends Controller
{
    public function index()
    {
    	return FaqTransformer::transform(Faq::all());
    }
}
