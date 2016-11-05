@extends('layouts.dashboard')

@section('pageTitle', 'Add New Post')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Post</h1>
		<a href="{{ route('dashboard.merchants.posts.index', $merchant->id) }}" class="btn btn-sm btn-warning">
			Cancel
		</a>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.posts.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="type">Post Type</label>
			<select name="type" id="type" class="form-control">
				<option value=""></option>
				<option value="notification">Notification</option>
				<option value="ticket">Ticket</option>
				<option value="promo">Promo</option>
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
			<label for="description">Description</label>
			<textarea name="description" id="description" class="form-control">
				{{ old('description') }}
			</textarea>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Save</button>
		</div>
	</form>
@endsection