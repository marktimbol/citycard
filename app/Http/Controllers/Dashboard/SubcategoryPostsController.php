<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Subcategory;
use App\Transformers\PostTransformer;
use Illuminate\Http\Request;

class SubcategoryPostsController extends Controller
{
    public function index(Subcategory $subcategory)
    {
		$posts = $subcategory->posts()->with(['category', 'outlets', 'merchant', 'photos', 'sources'])
				->latest()
				->paginate(config('pagination.count'));

		return view('dashboard.posts.index', compact('posts'));
    }
}
