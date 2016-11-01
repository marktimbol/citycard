@extends('layouts.dashboard')

@section('pageTitle', 'Add New Promo')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Promo</h1>
		<a href="{{ route('dashboard.merchants.promos.index', $merchant->id) }}" class="btn btn-warning">
			Cancel
		</a>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.promos.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="title">Title</label>
			<input type="text"
				name="title"
				id="title"
				value="{{ old('title') }}"
				class="form-control" />
		</div>
		@foreach( $outlets as $outlet )
		<div class="checkbox">
			<label>
				<input type="checkbox" name="outlet_ids[]" value="{{ $outlet->id }}" /> {{ $outlet->name }}
			</label>
		</div>
		@endforeach
		<div class="form-group">
			<button type="submit" class="btn btn-lg btn-primary">Save</button>
		</div>
	</form>
@endsection