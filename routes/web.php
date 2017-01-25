<?php

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

Route::get('auth/{provider}', 'Auth\SocialiteAuthController@redirect');
Route::get('auth/{provider}/callback', 'Auth\SocialiteAuthController@handle');

Route::get('/', ['as' => 'home', 'uses' => 'PagesController@home']);
Route::get('/posts', ['as' => 'posts.index', 'uses' => 'PostsController@index']);
Route::get('/events', ['as' => 'events', 'uses' => 'EventsController@index']);

Route::get('/explore', 'PagesController@explore');
Route::get('/about/company', 'PagesController@company');
Route::get('/about/faq', 'PagesController@faqs');
Route::get('/about/terms', 'PagesController@terms');
Route::get('/about/privacy', 'PagesController@privacy');
Route::get('/jobs', 'JobsController@index');

Auth::routes();
Route::get('/register/confirm/{token}', 'Auth\User\ConfirmEmailController@confirm');

Route::get('user/{user}', 'UsersController@show');

// Clerk password reset
Route::post('/clerk/password/reset', 'Auth\Clerk\ResetPasswordController@reset');
Route::get('/clerk/password/reset/{token}', 'Auth\Clerk\ResetPasswordController@showResetForm');