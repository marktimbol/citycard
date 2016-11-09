@extends('layouts.dashboard')

@section('pageTitle', 'Promo - '. $promo->title)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Promo: {{ $promo->title }}</h1>
		@include('dashboard._go-back')
	</div>

	<p>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	</p>

	<p>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
	</p>

	@include('dashboard._available-in', [
		'outlets' => $availableIn,
		'merchantOutlets' => $merchantOutlets
	])

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.promos.destroy', [$merchant->id, $promo->id])
	])

@endsection