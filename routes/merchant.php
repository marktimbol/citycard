<?php

Route::group([ 'domain' => merchantPath() ], function () {
	Route::get('login', 'Merchants\Auth\LoginController@showLoginForm');
	Route::post('login', [
		'as' => 'merchant.login', 
		'uses' => 'Merchants\Auth\LoginController@login'
	]);
	Route::group(['middleware' => 'auth:clerk', 'as' => 'clerk.'], function() {
		Route::get('/', ['as' => 'dashboard', 'uses' => 'Merchants\DashboardController@index']);

		Route::resource('merchants', 'Merchants\MerchantsController');
		Route::resource('outlets', 'Merchants\OutletsController');
		Route::resource('posts', 'Merchants\PostsController');
		Route::put('outlets/{outlet}/settings', [
			'as' => 'outlets.settings',
			'uses'	=> 'Merchants\OutletSettingsController@update'
		]);		
		Route::resource('clerks', 'Merchants\ClerksController');
	});
});

Route::group(['prefix' => 'merchants', 'as' => 'merchants.', 'middleware' => 'auth:merchant'], function() {

	Route::get('/', 'Merchants\DashboardController@index');

	Route::resource('posts', 'Merchants\MerchantPostsController', [
		'only'	=> ['index', 'create', 'store']
	]);
});