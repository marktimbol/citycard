@extends('layouts.dashboard')

@section('pageTitle', 'Outlet - '. $outlet->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Outlet: {{ $outlet->name }}</h1>
		@include('dashboard._go-back')
	</div>

	<ul class="list-group">
		<li class="list-group-item">
			Merchant: {{ $outlet->merchant->name }}
		</li>
		<li class="list-group-item">
			eMail: {{ $outlet->email }} <label class="label label-danger">Not Verified</label>
		</li>
		<li class="list-group-item">
			Phone: {{ $outlet->phone }} <label class="label label-danger">Not Verified</label>
		</li>
		<li class="list-group-item">
			Address: {{ sprintf('%s %s', $outlet->address1, $outlet->address2) }}
		</li>
		<li class="list-group-item">
			Latitude / Longitude: {{ sprintf('%s, %s', $outlet->latitude, $outlet->longitude) }}
		</li>
		<li class="list-group-item">
			Area:  {{ sprintf('%s, %s, %s', $outlet->area, $outlet->city, $outlet->country) }}
		</li>
		<li class="list-group-item">
			Status: <label class="label label-danger">Close</label>
		</li>
	</ul>

	<a href="#" class="btn btn-primary">
		Change Password
	</a>

	@include('dashboard._outlet-posts', [
		'posts'	=> $posts,
	])

	@include('dashboard._clerks')

	{{-- @include('dashboard._change-password') --}}

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.outlets.destroy', [$merchant->id, $outlet->id])
	])

@endsection