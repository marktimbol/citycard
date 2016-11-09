@extends('layouts.dashboard')

@section('pageTitle', $merchant->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $merchant->name }}</h1>
		@include('dashboard._go-back')
	</div>

	<ul class="list-group">
		<li class="list-group-item">
			eMail: {{ $merchant->email }} <label class="label label-danger">Not Verified</label>
		</li>
		<li class="list-group-item">
			Phone: {{ $merchant->phone }} <label class="label label-danger">Not Verified</label>
		</li>
		<li class="list-group-item">
			Address:  {{ sprintf('%s, %s', $merchant->city, $merchant->country) }}
		</li>
	</ul>

	<a href="#" class="btn btn-primary">
		Change Password
	</a>

	@include('dashboard.merchants._posts')

	@include('dashboard.merchants._outlets')

	@include('dashboard.merchants._clerks')	

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.destroy', $merchant->id)
	])
@endsection