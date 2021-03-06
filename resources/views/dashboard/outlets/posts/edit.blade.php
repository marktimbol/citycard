@extends('layouts.dashboard')

@section('pageTitle', 'Edit Post - '. $post->title)

@section('header_styles')
	<link href="{{ elixir('css/editor.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Post</h1>
		@include('dashboard._go-back')
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.posts.update', [$merchant->id, $post->id]) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}

		<div class="form-group">
			<label for="merchant">Merchant Name</label>
			<input type="text" name="merchant" id="merchant" class="form-control" value="{{ $merchant->name }}" disabled />
		</div>

		<div class="form-group">
			<label for="type">Post Type</label>
			<select name="type" id="type" class="form-control">
				<option value=""></option>
				<option value="notification" {{ $post->type == 'notification' ? 'selected' : '' }}>Notification</option>
				<option value="offer" {{ $post->type == 'offer' ? 'selected' : '' }}>Offer</option>
				<option value="ticket" {{ $post->type == 'ticket' ? 'selected' : '' }}>Ticket</option>
			</select>
		</div>		
		<div class="form-group">
			<label for="outlet_ids">Select Outlets</label>
			<select name="outlet_ids[]" id="outlet_ids" class="form-control" multiple>
				@foreach( $outlets as $outlet )
					<option value="{{ $outlet->id }}" {{ $post->outlets->contains($outlet->id) ? 'selected' : '' }}>
						{{ $outlet->name }}
					</option>
				@endforeach
			</select>
		</div>
	
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text"
				name="title"
				id="title"
				value="{{ old('title', $post->title) }}"
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
							id="price"
							value="{{ old('price', $post->price) }}"
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
				value="{{ old('link', $post->link) }}"
				class="form-control" />
		</div>

		<div class="form-group">
			<label for="editor">Description</label>
			<textarea name="desc" id="editor" class="form-control">
				{{ old('desc', $post->desc) }}
			</textarea>
		</div>

		<h3>Payment Option</h3>
		<div class="form-group">
			<label for="type">The customer can pay using</label>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="both" {{ $post->payment_option == 'both' ? 'checked' : '' }} /> Cashback &amp; Points
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="cashback" {{ $post->payment_option == 'cashback' ? 'checked' : '' }} /> Cashback
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="payment_option" value="points" {{ $post->payment_option == 'points' ? 'checked' : '' }} /> Points
				</label>
			</div>
		</div>	
		<div class="row">
			<div class="col-md-5">
				<div class="form-group">
					<label for="points">How many points the customer will earn when they purchased this offer?</label>
					<input type="text"
						name="points"
						id="points"
						value="{{ old('points', $post->points) }}"
						class="form-control" />
				</div>

			</div>
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
	<script src="{{ elixir('js/select.js') }}"></script>
@endsection