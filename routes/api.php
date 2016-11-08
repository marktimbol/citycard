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
	Route::post('login', 'Api\Auth\LoginController@login');
	Route::post('register', 'Api\Auth\RegisterController@register');
	Route::resource('outlets', 'Api\OutletsController');
	Route::resource('outlets.posts', 'Api\OutletPostsController');
	Route::resource('posts', 'Api\PostsController');
	Route::resource('offers', 'Api\OffersController');
	Route::resource('events', 'Api\EventsController');
	Route::resource('posts.purchase', 'Api\PurchasesController');
});
