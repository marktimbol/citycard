@extends('layouts.dashboard')

@section('pageTitle', 'Edit Merchant - '. $merchant->name)

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.edit', $merchant) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Merchant</h1>
	</div>

	<form method="POST" action="{{ route('dashboard.merchants.update', $merchant->id) }}">
		{{ csrf_field() }}
		{!! method_field('PUT') !!}

		<div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
			<label for="name" class="control-label">Name</label>
			<input type="text"
				name="name"
				id="name"
				class="form-control"
				value="{{ old('name') ?: $merchant->name }}" />
			@if( $errors->has('name') )
				<span class="help-block">
					{{ $errors->first('name') }}
				</span>
			@endif
		</div>

		<div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
			<label for="phone" class="label-block">Phone</label>
			<input type="tel"
				name="phone"
				id="phone"
				class="form-control"
				value="{{ old('phone') ?: $merchant->phone }}" />
			@if( $errors->has('phone') )
				<span class="help-block">
					{{ $errors->first('phone') }}
				</span>
			@endif				
		</div>

		<h3>What is your preferred currency?</h3>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group {{ $errors->has('currency') ? 'has-error' : '' }}">
					<label for="currency" class="control-label">Currency</label>
					<input type="text"
						name="currency"
						id="currency"
						class="form-control"
						value="{{ old('currency') ?: $merchant->currency }}" />
					@if( $errors->has('currency') )
						<span class="help-block">
							{{ $errors->first('currency') }}
						</span>
					@endif							
				</div>
			</div>
		</div>

		<h2>Account Details</h2>
		
		<div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
			<label for="email" class="control-label">Email</label>
			<input type="email"
				name="email"
				id="email"
				class="form-control"
				value="{{ old('email') ?: $merchant->email }}" />
			@if( $errors->has('email') )
				<span class="help-block">
					{{ $errors->first('email') }}
				</span>
			@endif					
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
					<label for="password" class="control-label">Password</label>
					<input type="password" name="password" value="" class="form-control" />
					@if( $errors->has('password') )
						<span class="help-block">
							{{ $errors->first('password') }}
						</span>
					@endif						
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="password_confirmation" class="control-label">Password Confirmation</label>
					<input type="password" name="password_confirmation" value="" class="form-control" />
				</div>
			</div>			
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