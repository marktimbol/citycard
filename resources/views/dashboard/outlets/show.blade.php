@extends('layouts.dashboard')

@section('pageTitle', 'Outlet - '. $outlet->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Outlet: {{ $outlet->name }}</h1>
		<a href="{{ route('dashboard.merchants.outlets.index', $merchant->id) }}" 
			class="btn btn-warning"
		>
			Cancel
		</a>
	</div>

	<ul class="list-group">
		<li class="list-group-item">
			Merchant: {{ $outlet->merchant->name }}
		</li>
		<li class="list-group-item">
			eMail: {{ $outlet->email }}
		</li>
		<li class="list-group-item">
			Phone: {{ $outlet->phone }}
		</li>
		<li class="list-group-item">
			Address: {{ sprintf('%s %s', $outlet->address1, $outlet->address2) }}
		</li>
		<li class="list-group-item">
			Latitude / Longitude: {{ sprintf('%s, %s', $outlet->latitude, $outlet->longitude) }}
		</li>
		<li class="list-group-item">
			Type: {{ $outlet->type }}
		</li>
		<li class="list-group-item">
			Area:  {{ sprintf('%s, %s, %s', $outlet->area, $outlet->city, $outlet->country) }}
		</li>
		<li class="list-group-item">
			Status: <label class="label label-success">Open</label>
		</li>
	</ul>

	@include('dashboard._outlet-promotions', [
		'promos'	=> $promos,
		'merchantPromos'	=> $merchantPromos
	])

	@include('dashboard._clerks')

	@include('dashboard._change-password')

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.outlets.destroy', [$merchant->id, $outlet->id])
	])

@endsection