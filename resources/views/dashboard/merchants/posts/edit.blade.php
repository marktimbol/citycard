@extends('layouts.dashboard')

@section('pageTitle', 'Edit Post - '. $post->title)

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Post</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.posts.update', [$merchant->id, $post->id]) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}

		<div class="form-group">
			<label for="merchant">Merchant Name</label>
			<input type="text" name="merchant" id="merchant" class="form-control" value="{{ $merchant->name }}" disabled />
		</div>

		<div class="form-group">
			<label for="title">Title</label>
			<input type="text"
				name="title"
				id="title"
				value="{{ old('title', $post->title) }}"
				class="form-control" />
		</div>

		<div class="form-group">
			<label for="editor">Description</label>
			<textarea name="desc" id="editor" class="form-control">
				{{ old('desc', $post->desc) }}
			</textarea>
		</div>

		<hr />
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Update</button>
			@include('dashboard._cancel')
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection