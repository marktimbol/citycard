<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('webhooks/vend', 'VendWebhooksController@store');

Route::group([
	'domain' => merchantPath(),
], function () {
	// Merchant Authentication
	Route::post('login', ['as' => 'api.clerk.login', 'uses' => 'Api\Auth\Clerk\LoginController@login']);	
	Route::delete('logout', ['as' => 'api.clerk.logout', 'uses' => 'Api\Auth\Clerk\LoginController@logout']);	
});

Route::group(['as' => 'api.'], function() {

	Route::post('/layer/identity_token', 'Api\LayerIdentityTokenController@store');	
	Route::post('/clerk/layer/identity_token', 'Api\ClerkLayerIdentityTokenController@store');	

	// User Authentication
	Route::post('login', 'Api\Auth\User\LoginController@login');
	Route::post('password/email', 'Api\Auth\User\ForgotPasswordController@sendResetLinkEmail');
	Route::post('register', 'Api\Auth\User\RegisterController@register');
	
	Route::post('clerk/password/email', 'Api\Auth\Clerk\ForgotPasswordController@sendResetLinkEmail');

	Route::get('search/{key}', 'Api\Search\SearchController@index');
	Route::get('search/deals/{key}', 'Api\Search\SearchDealsController@index');
	Route::get('search/events/{key}', 'Api\Search\SearchEventsController@index');
	Route::get('search/newsfeeds/{key}', 'Api\Search\SearchNewsFeedsController@index');
	Route::get('search/outlets/{key}', 'Api\Search\SearchOutletsController@index');

	// Get Merchant Outlets
	Route::get('merchants/{merchant}/outlets', 'Api\MerchantOutletsController@index');
	
	Route::get('outlets/nearme', 'Api\NearMeOutletsController@index');
	
	Route::resource('outlets', 'Api\OutletsController', [
		'only'	=> ['index', 'show']
	]);
	Route::resource('outlets.posts', 'Api\OutletPostsController', [
		'only'	=> ['index', 'show']
	]);
	Route::resource('outlets.photos', 'Api\OutletPhotosController', [
		'only'	=> ['index']
	]);
	Route::resource('posts', 'Api\PostsController', [
		'only'	=> ['index', 'show']
	]);
	Route::post('posts/{post}/favourite', ['uses' => 'Api\FavouritePostsController@store']);
	Route::delete('posts/{post}/unfavourite', ['uses' => 'Api\FavouritePostsController@destroy']);
	Route::resource('favourites', 'Api\FavouritesController', [
		'only'	=> ['index']
	]);
	Route::resource('deals', 'Api\DealsController', [
		'only'	=> ['index']
	]);
	Route::resource('events', 'Api\EventsController', [
		'only'	=> ['index']
	]);
	Route::resource('posts.purchase', 'Api\PurchasesController', [
		'only'	=> ['store']
	]);

	// Get all the rewards
	Route::get('rewards', 'Api\RewardsController@index');
	Route::get('rewards/{reward}', 'Api\RewardsController@show');
	
	// Get all the Outlets in a given Category
	Route::get('categories/only', 'Api\CategoriesOnlyController@index');
	Route::get('categories/{category}/outlets', 'Api\CategoryOutletsController@index');
	Route::get('categories/apply', 'Api\CategoryOutletsController@apply');
	
	// Filters
	// api/posts/?filter=yes&country=1&cities=1,2,3&categories=1,2,3&distance=&page=1
	// Route::get('posts/filter', 'Api\FilterPostsController@index');
	Route::get('filters', 'Api\FiltersController@index');

	// Countries
	Route::get('countries', 'Api\CountriesController@index');
	Route::get('countries/{country}/cities', 'Api\CountryCitiesController@index');
	Route::get('cities/{city}/areas', 'Api\CityAreasController@index');

	Route::get('categories', 'Api\CategoriesController@index');
	Route::get('categories/{category}/subcategories', 'Api\SubcategoriesController@index');

	Route::get('faqs', 'Api\FaqsController@index');
	Route::get('company', 'Api\CompanyController@index');
});
