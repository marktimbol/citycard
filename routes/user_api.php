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

	// User Purchase a Deal
	Route::post('user/posts/{post}/purchase', 'Api\User\PurchaseDealsController@store');

	// User checkout the order
	Route::post('user/checkout', 'Api\User\CheckoutController@store');

	// User Purchase a Reward
	Route::post('user/rewards/purchase', 'Api\User\PurchaseRewardsController@store');

	// Get all my vouchers
	Route::get('user/vouchers', 'Api\User\UserVouchersController@index');

	// Get all my transactions
	Route::get('user/transactions', 'Api\User\UserTransactionsController@index');
	
	// Get my total points
	Route::get('user/points', 'Api\User\UserPointsController@index');

	Route::get('user/outlets/following', 'Api\User\FollowingOutletsController@index');

	// Get all User reservations
	Route::get('user/reservations', 'Api\User\UserReservationsController@index');

	// User pending reservations count
	Route::get('user/reservations/count', 'Api\User\UserReservationsCountController@index');

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