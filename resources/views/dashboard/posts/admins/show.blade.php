@extends('layouts.dashboard')

@section('pageTitle', 'User')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $user->name }}</h1>
	</div>

	<p class="lead">Displsay how many posts that {{ $user->name }} created.</p>
@endsection
