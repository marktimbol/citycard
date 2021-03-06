@extends('layouts.dashboard')

@section('pageTitle', 'Edit Outlet - '. $outlet->name)

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Outlet</h1>
		@include('dashboard._go-back')
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.outlets.update', [$merchant->id, $outlet->id]) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}

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
				value="{{ old('name') ?: $outlet->name }}" />
		</div>

		<div class="form-group">
			<label for="phone" class="label-block">Phone</label>
			<input type="tel"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') ?: $outlet->phone }}" />
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="address1">Address 1</label>
					<input type="text"
						name="address1"
						id="address1"
						class="form-control"
						value="{{ old('address1') ?: $outlet->address1 }}" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="address2">Address 2</label>
					<input type="text"
						name="address2"
						id="address2"
						class="form-control"
						value="{{ old('address2') ?: $outlet->address2 }}" />
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
						value="{{ old('latitude') ?: $outlet->latitude }}" />
				</div>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="longitude">Longitude</label>
					<input type="text"
						name="longitude"
						id="longitude"
						class="form-control"
						value="{{ old('longitude') ?: $outlet->longitude }}" />
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<label for="area">Area</label>
					<input type="text"
						name="area"
						id="area"
						class="form-control"
						value="{{ old('area') ?: $outlet->area }}" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="city">City</label>
					<input type="text"
						name="city"
						id="city"
						class="form-control"
						value="{{ old('city') ?: $outlet->city }}" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="country">Country</label>
					<input type="text"
						name="country"
						id="country"
						class="form-control"
						value="{{ old('country') ?: $outlet->country }}" />
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
				value="{{ old('email') ?: $outlet->email }}" />
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">Update</button>
			@include('dashboard._cancel')
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
@endsection
