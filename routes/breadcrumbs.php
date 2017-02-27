<?php

Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('dashboard.index'));
});

// All Merchants
Breadcrumbs::register('merchants.index', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Merchants', route('dashboard.merchants.index'));
});

// Show Merchant
Breadcrumbs::register('merchants.show', function($breadcrumbs, $merchant)
{
    $breadcrumbs->parent('merchants.index');
    $breadcrumbs->push($merchant->name, route('dashboard.merchants.show', $merchant->id));
});

// Create Merchant
Breadcrumbs::register('merchants.create', function($breadcrumbs)
{
    $breadcrumbs->parent('merchants.index');
    $breadcrumbs->push('Create', '#');
});

// Edit Merchant
Breadcrumbs::register('merchants.edit', function($breadcrumbs, $merchant)
{
    $breadcrumbs->parent('merchants.show', $merchant);
    $breadcrumbs->push('Update Merchant', '#');
});

// All Merchant Outlets
Breadcrumbs::register('merchants.outlets.index', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Outlets', route('dashboard.merchants.outlets.index', $merchant->id));
});

// Show Outlet
Breadcrumbs::register('merchants.outlets.show', function($breadcrumbs, $outlet) {
	$breadcrumbs->parent('merchants.outlets.index', $outlet->merchant);
	$breadcrumbs->push($outlet->name, '#');
});

// Create Outlet
Breadcrumbs::register('merchants.outlets.create', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Create Outlet', '#');
});

// Edit Outlet
Breadcrumbs::register('merchants.outlets.edit', function($breadcrumbs, $outlet) {
	$breadcrumbs->parent('merchants.outlets.show', $outlet);
	$breadcrumbs->push('Update Outlet', '#');
});

// All Merchant Clerk
Breadcrumbs::register('merchants.clerks.index', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Clerks', route('dashboard.merchants.clerks.index', $merchant->id));
});

// Create Merchant Clerk
Breadcrumbs::register('merchants.clerks.create', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Create Clerk', '#');
});

// Show Clerk
Breadcrumbs::register('merchants.clerks.show', function($breadcrumbs, $clerk) {
	$breadcrumbs->parent('merchants.clerks.index', $clerk->merchant);
	$breadcrumbs->push($clerk->fullName(), '#');
});

// Edit Clerk
Breadcrumbs::register('merchants.clerks.edit', function($breadcrumbs, $clerk) {
	$breadcrumbs->parent('merchants.clerks.show', $clerk);
	$breadcrumbs->push('Update Clerk', '#');
});

// All Post
Breadcrumbs::register('merchants.posts.index', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Posts', route('dashboard.merchants.posts.index', $merchant->id));
});

// Create Post
Breadcrumbs::register('merchants.posts.create', function($breadcrumbs, $merchant) {
	$breadcrumbs->parent('merchants.show', $merchant);
	$breadcrumbs->push('Create Post', '#');
});

// Show Post
Breadcrumbs::register('merchants.posts.show', function($breadcrumbs, $post) {
	$breadcrumbs->parent('merchants.posts.index', $post->merchant);
	$breadcrumbs->push($post->title, route('dashboard.merchants.posts.show', [$post->merchant->id, $post->id]));
});

// Edit Post
Breadcrumbs::register('merchants.posts.edit', function($breadcrumbs, $post) {
	$breadcrumbs->parent('merchants.posts.show', $post);
	$breadcrumbs->push('Update Post', '#');
});



