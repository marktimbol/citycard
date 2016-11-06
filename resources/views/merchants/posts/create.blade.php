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
					<label for="outlet_ids" class="control-label">Post To</label>
					<select name="outlet_ids" id="outlet_ids" class="form-control" multiple>
						@foreach( $outlets as $outlet )
							<option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="type" class="control-label">Post Type</label>
					<select name="type" id="type" class="form-control">
						<option value=""></option>
						<option value="notification">Notification</option>
						<option value="offer">Offer</option>
						<option value="ticket">Ticket</option>
					</select>
				</div>
				<div class="form-group">
					<label for="title" class="control-label">Title</label>
					<input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" />
				</div>
				<div class="form-group">
					<label for="desc" class="control-label">Description</label>
					<textarea name="desc" id="desc" rows="10">
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
					<button type="submit" class="btn btn-primary">Save Post</button>
				</div>
			</form>	
		</div>
	</div>

@endsection