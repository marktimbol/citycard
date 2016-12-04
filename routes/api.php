<?php

use Illuminate\Http\Request;

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

Route::group(['as' => 'api.', 'middleware' => 'auth:user_api'], function() {
	Route::resource('clerks.messages', 'Api\User\MessagesController');
});

Route::group(['as' => 'api.', 'middleware' => 'auth:clerk_api'], function() {
	Route::resource('users.messages', 'Api\Clerk\MessagesController');
});

Route::group(['as' => 'api.'], function() {
	Route::post('login', 'Api\Auth\User\LoginController@login');
	Route::post('register', 'Api\Auth\User\RegisterController@register');
	Route::resource('outlets', 'Api\OutletsController');
	Route::resource('outlets.posts', 'Api\OutletPostsController');
	Route::resource('outlets.photos', 'Api\OutletPhotosController');
	Route::resource('posts', 'Api\PostsController');
	Route::resource('posts.favourites', 'Api\FavouritePostsController');
	Route::resource('deals', 'Api\DealsController');
	Route::resource('events', 'Api\EventsController');
	Route::resource('posts.purchase', 'Api\PurchasesController');
	
	// Filters
	// api/posts/?filter=yes&country=1&cities=1,2,3&categories=1,2,3&distance=&page=1
	Route::get('posts/filter', 'Api\FilterPostsController@index');
	Route::get('filters', 'Api\FiltersController@index');

	// Countries
	Route::get('countries', 'Api\CountriesController@index');
	Route::get('countries/{country}/cities', 'Api\CountryCitiesController@index');
	Route::get('cities/{city}/areas', 'Api\CityAreasController@index');

	Route::get('categories', 'Api\CategoriesController@index');
	Route::get('categories/{category}/subcategories', 'Api\SubcategoriesController@index');

});
