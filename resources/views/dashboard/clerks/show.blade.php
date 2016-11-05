@extends('layouts.dashboard')

@section('pageTitle', 'Clerk - '. sprintf('%s %s', $clerk->first_name, $clerk->last_name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Clerk: {{ sprintf('%s %s', $clerk->first_name, $clerk->last_name) }}</h1>
		<a href="{{ route('dashboard.merchants.clerks.index', $merchant->id) }}" class="btn btn-link">
			<i class="fa fa-long-arrow-left"></i> Go Back
		</a>
	</div>
	
	<ul class="list-group">
		<li class="list-group-item">
			eMail: {{ $clerk->email }}
		</li>
		<li class="list-group-item">
			Phone: {{ $clerk->phone }}
		</li>
		<li class="list-group-item">
			Address:  {{ sprintf('%s, %s', $clerk->city, $clerk->country) }}
		</li>
	</ul>

	@include('dashboard._working-in', [
		'outlets' => $outlets
	])

	@include('dashboard._change-password')

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.clerks.destroy', [$merchant->id, $clerk->id])
	])
@endsection