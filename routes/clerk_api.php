<?php

/*
|--------------------------------------------------------------------------
| Clerk API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'prefix' => 'clerk', 'middleware' => 'auth:clerk_api'], function() {

	Route::get('profile', 'Api\Clerk\ClerksController@index');

	Route::put('change-password', 'Api\Auth\Clerk\ChangePasswordController@update');

	Route::resource('users.messages', 'Api\Clerk\MessagesController', [
		'only'	=> ['store']
	]);

	Route::get('outlets', 'Api\Clerk\OutletsController@index');
	Route::get('outlets/{outlet}', 'Api\Clerk\OutletsController@show');

	// Create an item for reservation
	Route::post('outlets/{outlet}/items-for-reservation', 'Api\Clerk\OutletItemsForReservationController@store');

	// Confirm user's reservation
	Route::put('/outlets/{outlet}/reservations/{reservation}/confirm', 'Api\Clerk\ConfirmUserReservationController@update');
	
	Route::get('/outlets/{outlet}/reservations/cancelled', 'Api\Clerk\OutletCancelledReservationsController@index');
	
	// Add User as a Member of the Outlet
	Route::post('/outlets/{outlet}/members', 'Api\Clerk\OutletMembersController@store');

	// Outlet Reservations
	Route::resource('outlets.reservations', 'Api\Clerk\OutletReservationsController', [
		'only'	=> ['index', 'show']
	]);	

	// User reservation actions (modify and cancel)
	Route::resource('outlets.reservations', 'Api\Clerk\UserReservationsController', [
		'only'	=> ['update', 'destroy']
	]);

	// Outlet Photos
	Route::resource('outlets.photos', 'Api\Clerk\OutletPhotosController', [
		'only'	=> ['index', 'store', 'destroy']
	]);			

	// Outlet Menus
	Route::resource('outlets.menus', 'Api\Clerk\OutletMenusController', [
		'only'	=> ['index', 'store', 'destroy']
	]);

	// Outlet Shop Front
	Route::resource('outlets.shop_fronts', 'Api\Clerk\OutletShopFrontsController', [
		'only'	=> ['index', 'store', 'destroy']
	]);	

	Route::get('scan/{uuid}', 'Api\Clerk\ScanQRCodeController@show');
	Route::get('users/search/{key}', 'Api\Clerk\SearchUsersController@index');
	Route::get('users/{user}', 'Api\Clerk\UsersController@show');

	// Outlet User Notes
	Route::post('outlets/{outlet}/users/{user}/notes', 'Api\Clerk\OutletUserNotesController@store');
	Route::put('outlets/{outlet}/users/{user}/update-notes', 'Api\Clerk\OutletUserNotesController@update');
	Route::delete('outlets/{outlet}/users/{user}/delete-notes', 'Api\Clerk\OutletUserNotesController@destroy');

	// Redeem voucher for the User
	Route::put('outlets/{outlet}/vouchers/redeem', 'Api\Clerk\RedeemVouchersController@update');
});