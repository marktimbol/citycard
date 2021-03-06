@extends('layouts.dashboard')

@section('pageTitle', 'Edit Clerk - '. $clerk->name)

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.clerks.edit', $clerk) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Clerk</h1>
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

		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="display_name">Display Name</label>
					<input type="text"
						name="display_name"
						id="display_name"
						class="form-control"
						value="{{ old('display_name') ?: $clerk->display_name }}" />
				</div>
			</div>
		</div>		

		<div class="form-group">
			<label for="phone" class="label-block">Phone</label>
			<input type="tel"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') ?: $clerk->phone }}" />
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
			<button type="submit" class="btn btn-primary">Update</button>
			@include('dashboard._cancel')
		</div>
	</form>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
@endsection