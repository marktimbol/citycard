@extends('layouts.dashboard')

@section('pageTitle', 'Edit Post - '. $post->title)

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/datepicker.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Post</h1>
	</div>

	<div id="EditPost"></div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
	<script src="{{ elixir('js/EditPost.js') }}"></script>
@endsection