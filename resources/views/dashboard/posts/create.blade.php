@extends('layouts.dashboard')

@section('pageTitle', 'Add New Post')

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Post</h1>
	</div>

	<form method="POST" class="form-horizontal" action="{{ route('dashboard.merchants.posts.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="merchant" class="col-md-2 control-label">Merchant Name</label>
			<div class="col-md-10">
				<input type="text" name="merchant" id="merchant" class="form-control" value="{{ $merchant->name }}" disabled />
			</div>
		</div>
		<div class="form-group">
			<label for="type" class="col-md-2 control-label">Post Type</label>
			<div class="col-md-10">
				<select name="type" id="type" class="form-control">
					<option value="notification">Notification</option>
					<option value="offer">Offer</option>
					<option value="ticket">Ticket</option>
				</select>
			</div>
		</div>		
		<div class="form-group">
			<label for="outlet_ids" class="col-md-2 control-label">Select Outlets</label>
			<div class="col-md-10">
				<select name="outlet_ids" id="outlet_ids" class="form-control" multiple>
					@foreach( $outlets as $outlet )
					<option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
	
		<h3>Post Information</h3>

		<div class="form-group">
			<label for="title" class="col-md-2 control-label">Title</label>
			<div class="col-md-10">
				<input type="text"
					name="title"
					id="title"
					value="{{ old('title') }}"
					class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label for="price" class="col-md-2 control-label">Price</label>
			<div class="col-md-3">
				<div class="input-group">
					<input type="text"
						name="price"
						id="price
						value="{{ old('price') }}"
						class="form-control" />
					<span class="input-group-addon">AED</span>			
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="link" class="col-md-2 control-label">External Link</label>
			<div class="col-md-10">
				<input type="text"
					name="link"
					id="link"
					value="{{ old('link') }}"
					class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label for="editor" class="col-md-2 control-label">Description</label>
			<div class="col-md-10">
				<textarea name="desc" id="editor" class="form-control">
					{{ old('desc') }}
				</textarea>
			</div>
		</div>

		<h3>Payment Information</h3>

		<div class="form-group">
			<label for="type" class="col-md-4 control-label">The customer can pay using</label>
			<div class="col-md-6">
				<div class="radio-inline">
					<label>
						<input type="radio" name="payment_option" value="cashback" /> Cashback
					</label>
				</div>
				<div class="radio-inline">
					<label>
						<input type="radio" name="payment_option" value="points" /> Points
					</label>
				</div>
			</div>
		</div>	

		<div class="form-group">
			<label for="points" class="col-md-4 control-label">How many points the customer will earn when they purchase this offer?</label>
			<div class="col-md-3">
				<div class="input-group">
					<input type="text"
						name="points"
						id="points
						value="{{ old('points') }}"
						class="form-control" />
					<span class="input-group-addon">Points</span>
				</div>
			</div>
		</div>

		<hr />
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
	<script src="{{ elixir('js/select.js') }}"></script>
@endsection