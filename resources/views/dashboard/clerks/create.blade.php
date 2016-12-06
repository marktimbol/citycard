@extends('layouts.dashboard')

@section('pageTitle', 'Add New Clerk')

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create Clerk</h1>
	</div>

	@include('errors.list')
	
	<form method="POST" action="{{ route('dashboard.merchants.clerks.store', $merchant->id) }}">
		{{ csrf_field() }}
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="merchant">Merchant Name</label>
					<input type="text" value="{{ $merchant->name }}" class="form-control" disabled />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="first_name">First Name</label>
					<input type="text"
						name="first_name"
						id="first_name"
						class="form-control"
						value="{{ old('first_name') }}" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="last_name">Last Name</label>
					<input type="text"
						name="last_name"
						id="last_name"
						class="form-control"
						value="{{ old('last_name') }}" />
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="phone" class="label-block">Phone</label>
			<input type="tel"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') }}" />
		</div>

		<h2>Account Details</h2>

		<div class="form-group">
			<label for="email">Email</label>
			<input type="email"
				name="email"
				id="email"
				class="form-control"
				value="@citycard.me" />
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
			@include('dashboard._cancel')
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
@endsection
