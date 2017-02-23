<?php

namespace App\Http\Controllers\Api;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\CategoryOutletsTransformer;

class CategoryOutletsController extends Controller
{
	public function index(Category $category)
	{
		$category->load('outlets.merchant', 'outlets.posts');

		return CategoryOutletsTransformer::transform($category->outlets);
	}
}
