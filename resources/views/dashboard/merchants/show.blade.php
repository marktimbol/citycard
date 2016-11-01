@extends('layouts.dashboard')

@section('pageTitle', 'Merchant - '. $merchant->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Merchant: {{ $merchant->name }}</h1>
		<a href="{{ route('dashboard.merchants.index') }}" class="btn btn-warning">Back</a>
	</div>

	<ul class="list-group">
		<li class="list-group-item">
			eMail: {{ $merchant->email }} <label class="label label-success">Verified</label>
		</li>
		<li class="list-group-item">
			Phone: {{ $merchant->phone }} <label class="label label-danger">Not Verified</label>
		</li>
		<li class="list-group-item">
			Address:  {{ sprintf('%s, %s', $merchant->city, $merchant->country) }}
		</li>
	</ul>

	<a href="" class="btn btn-default">
		Change Password
	</a>

	@include('dashboard.merchants._outlets')

	@include('dashboard.merchants._clerks')	

	<h2>Promotions</h2>

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.destroy', $merchant->id)
	])
@endsection