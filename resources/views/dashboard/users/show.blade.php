@extends('layouts.dashboard')

@section('pageTitle', 'User')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $user->name }}</h1>
	</div>

	<p class="lead">Display user information</p>
@endsection
