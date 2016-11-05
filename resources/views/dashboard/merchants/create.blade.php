@extends('layouts.dashboard')

@section('pageTitle', 'Add New Merchant')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Merchant</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.store') }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text"
				name="name"
				id="name"
				class="form-control"
				value="{{ old('name') }}" />
		</div>

		<div class="form-group">
			<label for="phone">Phone</label>
			<input type="text"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') }}" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="city">City</label>
					<input type="text"
						name="city"
						id="city"
						class="form-control"
						value="{{ old('city') }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="country">Country</label>
					<input type="text"
						name="country"
						id="country"
						class="form-control"
						value="{{ old('country') }}" />
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
				value="{{ old('email') }}" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password"
						name="password"
						id="password"
						class="form-control"
						value="{{ old('password') }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="password_confirmation">Password Confirmation</label>
					<input type="password"
						name="password_confirmation"
						id="password_confirmation"
						class="form-control" />
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Save</button>
			<a href="{{ route('dashboard.merchants.index') }}" class="btn btn-link">Cancel</a>
		</div>
	</form>
@endsection