@extends('layouts.dashboard')

@section('pageTitle', 'Add New Post')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create Post</h1>
	</div>

	<div id="CreatePost"></div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
	<script src="{{ elixir('js/CreatePost.js') }}"></script>
@endsection
