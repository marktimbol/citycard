<?php

use Illuminate\Support\Facades\Storage;

function getPhotoPath($photo) {
	if( ! $photo ) {
		return null;
	}

	return sprintf('https://s3-%s.amazonaws.com/%s/%s',
			config('filesystems.disks.s3.region'),
			config('filesystems.disks.s3.bucket'),
			$photo
	);
}

function deletePhoto($photo) {
	Storage::delete($photo);
}

function adminPath() {
	return env('CITYCARD_ADMIN');
}

function merchantPath() {
	return env('CITYCARD_MERCHANT');
}

function adminCan($action, $model)
{
	return auth()->guard('admin')->user()->can($action, $model);
}

function userCan($action, $model, $guard='admin')
{
	return auth()->guard($guard)->user()->can($action, $model);
}

function isAdmin()
{
	return auth()->guard('admin')->user()->hasRole('admin');
}

function permissionIsEnabled()
{
	return env('ACTIVATE_PERMISSION');
}