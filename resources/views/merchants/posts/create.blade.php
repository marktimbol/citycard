@extends('layouts.merchant')


@section('content')

	<div class="row">
		<div class="col-md-3">

		</div>
		<div class="col-md-9">
			<h1>Create Post</h1>

			<form method="POST" action="{{ route('merchants.posts.store') }}">
				{{ csrf_field() }}
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
					<button type="submit" class="btn btn-primary">Save Post</button>
				</div>
			</form>	
		</div>
	</div>

@endsection