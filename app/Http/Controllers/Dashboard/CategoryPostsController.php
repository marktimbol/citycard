<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use JavaScript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryPostsController extends Controller
{
    public function index(Category $category)
    {
		$posts = $category->posts()
			->with(['category', 'outlets', 'merchant', 'photos', 'sources'])
			->latest()
			->paginate(config('pagination.count'));

		JavaScript::put([
			'posts'	=> $posts
		]);

		return view('dashboard.posts.index', compact('posts'));
    }
}
