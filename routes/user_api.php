<?php

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'middleware' => 'auth:user_api'], function() {
	Route::resource('clerks.messages', 'Api\User\MessagesController', [
		'only'	=> ['store']
	]);
	Route::put('user/email', 'Api\User\UpdateEmailController@update');
	Route::put('user/mobile', 'Api\User\UpdateMobileController@update');
	Route::put('user/change-password', 'Api\Auth\User\ChangePasswordController@update');
	Route::post('user/photo', 'Api\User\UserPhotosController@store');
	Route::post('user/qrcode', 'Api\User\UserQRCodeController@store');
	Route::get('user/profile', 'Api\User\ProfileController@index');
	Route::put('user/profile', 'Api\User\ProfileController@update');
	Route::post('user/invites', 'Api\User\InvitesController@store');

	// Get all User reservations
	Route::get('user/reservations', 'Api\User\UserReservationsController@index');

	// User pending reservations count
	Route::get('user/reservations/pending/count', 'Api\User\UserPendingReservationsCountController@index');

	// Show single reserviation
	Route::get('user/reservations/{reservation}', 'Api\User\UserReservationsController@show');
	
	// Get all the outlet items available for reservation
	Route::get('outlets/{outlet}/items-for-reservation', 'Api\Outlet\ItemsForReservationController@index');
	Route::get('outlets/{outlet}/items-for-reservation/{item}', 'Api\Outlet\ItemsForReservationController@show');

	// Get all the items for reservation
	Route::get('posts/{post}/reservation-options', 'Api\Post\ItemsForReservationController@index');
	
	// Reservation actions (reserve, modify & cancel)
	Route::resource('outlets.reservations', 'Api\User\OutletReservationsController', [
		'only'	=> ['store', 'update', 'destroy']
	]);

	// User follows an outlet
	Route::post('outlets/{outlet}/follows', 'Api\User\OutletFollowersController@store');
	// User unfollows an outlet
	Route::delete('outlets/{outlet}/unfollow', 'Api\User\OutletFollowersController@destroy');
});