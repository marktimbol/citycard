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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:user_api');

Route::group(['as' => 'api.'], function() {
	Route::post('login', 'Api\Auth\LoginController@login');
	Route::post('register', 'Api\Auth\RegisterController@register');
	Route::resource('outlets', 'Api\OutletsController');
	Route::resource('outlets.posts', 'Api\OutletPostsController');
	Route::resource('posts.purchase', 'Api\PurchasesController');
});
