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

Route::group([
	'domain' => merchantPath(),
], function () {
	// Merchant Authentication
	Route::post('login', ['as' => 'api.clerk.login', 'uses' => 'Api\Auth\Clerk\LoginController@login']);	
});

Route::group(['as' => 'api.', 'middleware' => 'auth:user_api'], function() {
	Route::resource('clerks.messages', 'Api\User\MessagesController');
	Route::put('user/change-password', 'Api\User\ChangePasswordController@update');
	Route::put('user/profile', 'Api\User\ProfileController@update');

	// User reserve a services from the outlet
	Route::post('outlets/{outlet}/reservations', 'Api\User\OutletReservationsController@store');
	// User follows an outlet
	Route::post('outlets/{outlet}/follows', 'Api\User\OutletFollowersController@store');
	Route::delete('outlets/{outlet}/unfollow', 'Api\User\OutletFollowersController@destroy');
});

Route::group(['as' => 'api.', 'middleware' => 'auth:clerk_api'], function() {
	Route::resource('users.messages', 'Api\Clerk\MessagesController');
});

Route::group(['as' => 'api.'], function() {
	// User Authentication
	Route::post('login', 'Api\Auth\User\LoginController@login');
	Route::post('password/email', 'Api\Auth\User\ForgotPasswordController@sendResetLinkEmail');
	Route::post('register', 'Api\Auth\User\RegisterController@register');
	
	Route::resource('outlets', 'Api\OutletsController');
	Route::resource('outlets.posts', 'Api\OutletPostsController');
	Route::resource('outlets.photos', 'Api\OutletPhotosController');
	Route::resource('posts', 'Api\PostsController');
	Route::post('posts/{post}/favourite', ['uses' => 'Api\FavouritePostsController@store']);
	Route::delete('posts/{post}/unfavourite', ['uses' => 'Api\FavouritePostsController@destroy']);
	Route::resource('favourites', 'Api\FavouritesController');
	Route::resource('deals', 'Api\DealsController');
	Route::resource('events', 'Api\EventsController');
	Route::resource('posts.purchase', 'Api\PurchasesController');
	
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

});
