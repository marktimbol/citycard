@extends('layouts.dashboard')

@section('pageTitle', 'Add New Post')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add New Post</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.outlets.posts.store', $outlet->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="outlet">Outlet Name</label>
			<input type="text" name="outlet" id="outlet" class="form-control" value="{{ $outlet->name }}" disabled />
		</div>
		<div class="form-group">
			<label for="type">Post Type</label>
			<select name="type" id="type" class="form-control">
				<option value="">Select option</option>
				<option value="notification">Notification</option>
				<option value="offer">Offer</option>
				<option value="ticket">Ticket</option>
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

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="price">Price</label>
					<div class="input-group">
						<span class="input-group-addon">AED</span>			
						<input type="text"
							name="price"
							id="price
							value="{{ old('price') }}"
							class="form-control" />
					</div>
				</div>
			</div>
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
			<label for="editor">Description</label>
			<textarea name="desc" id="editor" class="form-control">
				{{ old('desc') }}
			</textarea>
		</div>

		<h3>Payment Option</h3>
		<div class="form-group">
			<label for="type">The customer can pay using</label>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="both" /> Cashback &amp; Points
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="cashback" /> Cashback
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="points" /> Points
				</label>
			</div>
		</div>	
		<div class="row">
			<div class="col-md-5">
				<div class="form-group">
					<label for="points">How many points the customer will earn when they purchased this offer?</label>
					<input type="text"
						name="points"
						id="points
						value="{{ old('points') }}"
						class="form-control" />
				</div>

			</div>
		</div>
		<hr />
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Save</button>
			<a href="{{ route('dashboard.outlets.posts.index', $outlet->id) }}" class="btn btn-link">
				Cancel
			</a>
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/editor.js') }}"></script>
	<script src="{{ elixir('js/select.js') }}"></script>
@endsection