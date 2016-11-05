@extends('layouts.dashboard')

@section('pageTitle', 'Add New Outlet')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Outlet</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.outlets.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="form-group">
			<label for="merchant">Merchant Name</label>
			<input type="text" value="{{ $merchant->name }}" class="form-control" disabled />
		</div>
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
					<label for="address1">Address 1</label>
					<input type="text"
						name="address1"
						id="address1"
						class="form-control"
						value="{{ old('address1') }}" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="address2">Address 2</label>
					<input type="text"
						name="address2"
						id="address2"
						class="form-control"
						value="{{ old('address2') }}" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="latitude">Latitude</label>
					<input type="text"
						name="latitude"
						id="latitude"
						class="form-control"
						value="{{ old('latitude') }}" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="longitude">Longitude</label>
					<input type="text"
						name="longitude"
						id="longitude"
						class="form-control"
						value="{{ old('longitude') }}" />
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="type">Type</label>
			<select name="type" class="form-control">
				<option value="Main">Main</option>
				<option value="Branch">Branch</option>
			</select>
		</div>
		
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="area">Area</label>
					<input type="text"
						name="area"
						id="area"
						class="form-control"
						value="{{ old('area') }}" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="city">City</label>
					<input type="text"
						name="city"
						id="city"
						class="form-control"
						value="{{ old('city') }}" />
				</div>
			</div>
			<div class="col-md-4">
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
			<a href="{{ route('dashboard.merchants.outlets.index', $merchant->id) }}" 
				class="btn btn-link"
			>
				Cancel
			</a>
		</div>
	</form>
@endsection