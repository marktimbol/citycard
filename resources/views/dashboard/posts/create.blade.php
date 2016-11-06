@extends('layouts.dashboard')

@section('pageTitle', 'Add New Post')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Post</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.posts.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="merchant">Merchant Name</label>
			<input type="text" name="merchant" id="merchant" class="form-control" value="{{ $merchant->name }}" disabled />
		</div>
		<div class="form-group">
			<label for="type">Post Type</label>
			<select name="type" id="type" class="form-control">
				<option value=""></option>
				<option value="notification">Notification</option>
				<option value="offer">Offer</option>
				<option value="ticket">Ticket</option>
			</select>
		</div>		
		<div class="form-group">
			<label for="outlet_ids">Select Outlets</label>
			<select name="outlet_ids" id="outlet_ids" class="form-control" multiple>
				@foreach( $outlets as $outlet )
				<option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
				@endforeach
			</select>
		</div>
	
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text"
				name="title"
				id="title"
				value="{{ old('title') }}"
				class="form-control" />
		</div>

		<div class="form-group">
			<label for="editor">Description</label>
			<textarea name="desc" id="editor" class="form-control">
				{{ old('desc') }}
			</textarea>
		</div>

		<div class="form-group">
			<label for="link">External Link</label>
			<input type="text"
				name="link"
				id="link"
				value="{{ old('link') }}"
				class="form-control" />
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Save</button>
			<a href="{{ route('dashboard.merchants.posts.index', $merchant->id) }}" class="btn btn-link">
				Cancel
			</a>
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
@endsection