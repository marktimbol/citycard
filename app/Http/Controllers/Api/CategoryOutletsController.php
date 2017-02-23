<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Merchant;
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

	public function apply()
	{
		$merchants = Merchant::with('outlets:id,merchant_id,name', 'categories:id')
			->select('id','name')
			->get();

		foreach( $merchants as $merchant ) {
			foreach( $merchant->outlets as $outlet ) {
				$outlet->categories()->sync($merchant->categories->pluck('id'));
			}
		}

		return 'Done';
	}
}
