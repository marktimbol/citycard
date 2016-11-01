@extends('layouts.dashboard')

@section('pageTitle', 'Edit Merchant - '. $merchant->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Merchant</h1>
		<a href="{{ route('dashboard.merchants.index') }}" class="btn btn-warning">Cancel</a>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.update', $merchant->id) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}

		<div class="form-group">
			<label for="name">Name</label>
			<input type="text"
				name="name"
				id="name"
				class="form-control"
				value="{{ old('name') ?: $merchant->name }}" />
		</div>

		<div class="form-group">
			<label for="phone">Phone</label>
			<input type="text"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') ?: $merchant->phone }}" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="city">City</label>
					<input type="text"
						name="city"
						id="city"
						class="form-control"
						value="{{ old('city') ?: $merchant->city }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="country">Country</label>
					<input type="text"
						name="country"
						id="country"
						class="form-control"
						value="{{ old('country') ?: $merchant->country }}" />
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
				value="{{ old('email') ?: $merchant->email }}" />
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary btn-lg">Update</button>
		</div>
	</form>

@endsection