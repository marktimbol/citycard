@extends('layouts.dashboard')

@section('pageTitle', $category->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $category->name }}</h1>
		@include('dashboard._go-back')
	</div>

	@include('dashboard._delete', [
		'route'	=> route('dashboard.categories.destroy', $category->id)
	])
@endsection
