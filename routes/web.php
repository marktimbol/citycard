<?php

use App\ItemForReservation;
use App\Post;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/for-reservation/{item}', function(ItemForReservation $item) {
	return $item;
});

Route::get('auth/{provider}', 'Auth\SocialiteAuthController@redirect');
Route::get('auth/{provider}/callback', 'Auth\SocialiteAuthController@handle');

Route::get('/', ['as' => 'home', 'uses' => 'PagesController@home']);
Route::get('/posts', ['as' => 'posts.index', 'uses' => 'PostsController@index']);

Route::group([
	'prefix' => 'user', 
	'middleware' => 'auth:user'
], function() {
	Route::get('{user}', 'UsersController@show');
});


Auth::routes();
Route::get('/register/confirm/{token}', 'Auth\ConfirmEmailController@confirm');

Route::get('/home', 'HomeController@index');
