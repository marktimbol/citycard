@extends('layouts.dashboard')

@section('pageTitle', 'Edit Clerk - '. $clerk->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Clerk</h1>
		<a href="{{ route('dashboard.merchants.clerks.index', $merchant->id) }}" class="btn btn-warning">Cancel</a>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.clerks.update', [$merchant->id, $clerk->id]) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="first_name">First Name</label>
					<input type="text"
						name="first_name"
						id="first_name"
						class="form-control"
						value="{{ old('first_name') ?: $clerk->first_name }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="last_name">Last Name</label>
					<input type="text"
						name="last_name"
						id="last_name"
						class="form-control"
						value="{{ old('last_name') ?: $clerk->last_name }}" />
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="phone">Phone</label>
			<input type="text"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') ?: $clerk->phone }}" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="city">City</label>
					<input type="text"
						name="city"
						id="city"
						class="form-control"
						value="{{ old('city') ?: $clerk->city }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="country">Country</label>
					<input type="text"
						name="country"
						id="country"
						class="form-control"
						value="{{ old('country') ?: $clerk->country }}" />
				</div>
			</div>
		</div>

		<h2>Account Details</h2>
		
		<div class="form-group">
			<label for="email">Email</label>
			<input type="email"
				name="email"
				id="email"
				class="form-control"
				value="{{ old('email') ?: $clerk->email }}" />
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-lg">Update</button>
		</div>
	</form>
@endsection