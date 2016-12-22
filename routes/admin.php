<?php

Route::group([
	'domain' => adminPath(),
	], function () {
		// Admin Authentication Routes...
		Route::get('/', 'Auth\Admin\LoginController@showLoginForm');
		Route::post('/', 'Auth\Admin\LoginController@login');
		Route::get('/login', 'Auth\Admin\LoginController@showLoginForm');
		Route::post('/login', 'Auth\Admin\LoginController@login');
		Route::post('logout', 'Auth\Admin\LoginController@logout');
		// Admin Registration Routes...
		Route::get('register', 'Auth\Admin\RegisterController@showRegistrationForm');
		Route::post('register', 'Auth\Admin\RegisterController@register');
});

Route::group([
	'domain' => adminPath(),
	'as'	=> 'dashboard.',
	'prefix'	=> 'dashboard',
	'middleware' => 'auth:admin'
], function () {
	Route::get('/', ['as' => 'index', 'uses' => 'DashboardController@index']);
	// Route::get('/attach-existing', ['as' => 'attach.existing', 'uses' => 'DashboardController@attachExisting']);
	// Route::get('/attach-posts', ['as' => 'attach.posts', 'uses' => 'DashboardController@attachPosts']);
	Route::resource('users', 'Dashboard\UsersController');
	Route::resource('admins', 'Dashboard\AdminsController');
	Route::resource('roles', 'Dashboard\RolesController');
	Route::resource('permissions', 'Dashboard\PermissionsController');
	Route::resource('roles.permissions', 'Dashboard\RolePermissionsController');
	Route::resource('admins.roles', 'Dashboard\AdminRolesController');

	Route::get('merchants/testing', 'Dashboard\MerchantsController@testing');
	Route::resource('merchants', 'Dashboard\MerchantsController');
	Route::resource('merchants.outlets', 'Dashboard\MerchantOutletsController');
	Route::resource('merchants.clerks', 'Dashboard\MerchantClerksController');
	Route::resource('merchants.promos', 'Dashboard\PromosController');
	Route::resource('merchants.posts', 'Dashboard\MerchantPostsController');
	Route::put('merchants/{merchant}/photos', 'Dashboard\MerchantPhotosController@update');
	Route::resource('clerks', 'Dashboard\ClerksController');
	Route::resource('clerks.outlets', 'Dashboard\ClerksOutletsController');
	Route::put('clerks/{clerk}/photos', 'Dashboard\ClerkPhotosController@update');

	Route::post('posts/publish', [
		'as' => 'posts.publish',
		'uses' => 'Dashboard\PublishPostsController@store'
	]);

	Route::delete('posts/unpublish', [
		'as' => 'posts.unpublish',
		'uses' => 'Dashboard\PublishPostsController@destroy'
	]);

	Route::get('posts/publish-all', 'Dashboard\PublishPostsController@publishAll');

	Route::resource('posts', 'Dashboard\PostsController');
	Route::resource('posts.photos', 'Dashboard\PostPhotosController');

	Route::resource('outlets', 'Dashboard\OutletsController');
	Route::resource('outlets.photos', 'Dashboard\OutletPhotosController');
	Route::resource('outlets.posts', 'Dashboard\OutletPostsController');
	Route::resource('outlets.clerks', 'Dashboard\OutletClerksController');
	Route::resource('outlets.for-reservations', 'Dashboard\ItemsForReservationController');
	Route::put('outlets/{outlet}/activate-reservation', [
		'as' => 'outlet.activate-reservation',
		'uses' => 'Dashboard\ActivateOutletReservationController@update'
	]);	

	Route::delete('outlets/{outlet}/deactivate-reservation', [
		'as' => 'outlet.deactivate-reservation',
		'uses' => 'Dashboard\DeactivateOutletReservationController@destroy'
	]);		

	Route::resource('countries', 'Dashboard\CountriesController');
	Route::resource('countries.cities', 'Dashboard\CountryCitiesController');
	Route::resource('cities.areas', 'Dashboard\CityAreasController');
	Route::post('import/cities/{city}/areas', 'Dashboard\ImportAreasController@store');
	Route::resource('sources', 'Dashboard\SourcesController');
	Route::resource('sources.posts', 'Dashboard\ExternalPostsController');
	Route::resource('categories', 'Dashboard\CategoriesController');
	Route::resource('categories.subcategories', 'Dashboard\SubcategoriesController');
	Route::resource('categories.posts', 'Dashboard\CategoryPostsController');
	Route::resource('subcategories.posts', 'Dashboard\SubcategoryPostsController');
	// Route::resource('staffs', 'Dashboard\StaffsController');
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

// Route::get('/admin/dashboard', function() {
// 	return 'Admin Dashboard';
// });

// Route::group([
// 	'as' => 'dashboard.',
// 	'prefix' => 'dashboard',
// 	'middleware' => 'auth:admin'
// ], function() {
// 	Route::get('/', ['as' => 'index', 'uses' => 'DashboardController@index']);
// 	Route::resource('merchants', 'Dashboard\MerchantsController');
// 	Route::resource('merchants.outlets', 'Dashboard\MerchantOutletsController');
// 	Route::resource('merchants.clerks', 'Dashboard\MerchantClerksController');
// 	Route::resource('merchants.promos', 'Dashboard\PromosController');
// 	Route::resource('merchants.posts', 'Dashboard\MerchantPostsController');
// 	Route::put('merchants/{merchant}/photos', 'Dashboard\MerchantPhotosController@update');
// 	Route::resource('clerks', 'Dashboard\ClerksController');
// 	Route::put('clerks/{clerk}/photos', 'Dashboard\ClerkPhotosController@update');
// 	Route::resource('posts', 'Dashboard\PostsController');
// 	Route::resource('posts.photos', 'Dashboard\PostPhotosController');
// 	Route::resource('outlets.photos', 'Dashboard\OutletPhotosController');
// 	Route::resource('outlets.posts', 'Dashboard\OutletPostsController');
// 	Route::resource('outlets.clerks', 'Dashboard\OutletClerksController');
// 	Route::resource('countries', 'Dashboard\CountriesController');
// 	Route::resource('countries.cities', 'Dashboard\CountryCitiesController');
// 	Route::resource('cities.areas', 'Dashboard\CityAreasController');
// 	Route::post('import/cities/{city}/areas', 'Dashboard\ImportAreasController@store');
// 	Route::resource('sources', 'Dashboard\SourcesController');
// 	Route::resource('sources.posts', 'Dashboard\ExternalPostsController');
// 	Route::resource('categories', 'Dashboard\CategoriesController');
// 	Route::resource('categories.subcategories', 'Dashboard\SubcategoriesController');
// 	Route::resource('categories.posts', 'Dashboard\CategoryPostsController');
// 	Route::resource('subcategories.posts', 'Dashboard\SubcategoryPostsController');
// 	Route::post('import/categories', 'Dashboard\ImportCategoriesController@store');
// 	Route::post('import/categories/{category}/subcategories', 'Dashboard\ImportSubcategoriesController@store');

// 	//
// 	Route::post('merchants/{merchant}/outlets/{outlet}/promos', [
// 		'as' => 'merchants.outlets.promos.store',
// 		'uses' => 'Dashboard\OutletPromosController@store'
// 	]);
// 	Route::delete('merchants/{merchant}/outlets/{outlet}/promos/{promo}', [
// 		'as' => 'merchants.outlets.promos.destroy',
// 		'uses' => 'Dashboard\OutletPromosController@destroy'
// 	]);
// 	Route::post('merchants/{merchant}/promos/{promo}/outlets', [
// 		'as' => 'merchants.promos.outlets.store',
// 		'uses' => 'Dashboard\PromoOutletsController@store'
// 	]);
// 	Route::delete('merchants/{merchant}/promos/{promo}/outlets/{outlet}', [
// 		'as' => 'merchants.promos.outlets.destroy',
// 		'uses' => 'Dashboard\PromoOutletsController@destroy'
// 	]);
// });
