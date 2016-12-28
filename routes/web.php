<?php

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

Route::get('/', ['as' => 'home', 'uses' => 'PagesController@home']);
Route::get('/posts', ['as' => 'posts.index', 'uses' => 'PostsController@index']);

Auth::routes();
Route::get('/register/confirm/{token}', 'Auth\ConfirmEmailController@confirm');

Route::get('/home', 'HomeController@index');
