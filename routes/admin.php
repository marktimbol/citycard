<?php

// Admin Authentication Routes...
Route::get('/admin/login', 'Auth\Admin\LoginController@showLoginForm');
Route::post('/admin/login', 'Auth\Admin\LoginController@login');
Route::delete('/admin/logout', 'Auth\Admin\LoginController@logout');

// Admin Registration Routes...
Route::get('/admin/register', 'Auth\Admin\RegisterController@showRegistrationForm');
Route::post('/admin/register', 'Auth\Admin\RegisterController@register');

Route::get('/admin/dashboard', function() {
	return 'Admin Dashboard';
});

Route::group([
	'as' => 'dashboard.',
	'prefix' => 'dashboard',
	'middleware' => 'auth:admin'
], function() {
	Route::get('/', ['as' => 'index', 'uses' => 'DashboardController@index']);
	Route::resource('merchants', 'Dashboard\MerchantsController');
	Route::resource('merchants.outlets', 'Dashboard\MerchantOutletsController');
	Route::resource('merchants.clerks', 'Dashboard\MerchantClerksController');
	Route::resource('merchants.promos', 'Dashboard\PromosController');
	Route::resource('merchants.posts', 'Dashboard\MerchantPostsController');
	Route::resource('posts.photos', 'Dashboard\PostPhotosController');
	Route::resource('outlets.photos', 'Dashboard\OutletPhotosController');
	Route::resource('outlets.posts', 'Dashboard\OutletPostsController');
	Route::resource('outlets.clerks', 'Dashboard\OutletClerksController');
	Route::resource('countries', 'Dashboard\CountriesController');
	Route::resource('countries.cities', 'Dashboard\CountryCitiesController');
	Route::resource('cities.areas', 'Dashboard\CityAreasController');
	Route::post('import/cities/{city}/areas', 'Dashboard\ImportAreasController@store');
	Route::resource('sources', 'Dashboard\SourcesController');
	Route::resource('categories', 'Dashboard\CategoriesController');
	Route::resource('categories.subcategories', 'Dashboard\SubcategoriesController');
	Route::post('import/categories', 'Dashboard\ImportCategoriesController@store');
	Route::post('import/categories/{category}/subcategories', 'Dashboard\ImportSubcategoriesController@store');

	//
	Route::post('merchants/{merchant}/outlets/{outlet}/promos', [
		'as' => 'merchants.outlets.promos.store',
		'uses' => 'Dashboard\OutletPromosController@store'
	]);
	Route::delete('merchants/{merchant}/outlets/{outlet}/promos/{promo}', [
		'as' => 'merchants.outlets.promos.destroy',
		'uses' => 'Dashboard\OutletPromosController@destroy'
	]);
	Route::post('merchants/{merchant}/promos/{promo}/outlets', [
		'as' => 'merchants.promos.outlets.store',
		'uses' => 'Dashboard\PromoOutletsController@store'
	]);
	Route::delete('merchants/{merchant}/promos/{promo}/outlets/{outlet}', [
		'as' => 'merchants.promos.outlets.destroy',
		'uses' => 'Dashboard\PromoOutletsController@destroy'
	]);
});
