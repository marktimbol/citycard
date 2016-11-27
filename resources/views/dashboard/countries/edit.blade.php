@extends('layouts.dashboard')

@section('pageTitle', 'Edit Country')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Edit {{ $country->name }}</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.countries.update', $country->id) }}">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						value="{{ $country->name }}"
						placeholder="etc. United Arab Emirates" />
				</div>

				<div class="form-group">
					<label for="iso_code">ISO Code</label>
					<input
						type="text"
						name="iso_code"
						id="iso_code"
						class="form-control"
						value="{{ $country->iso_code }}"
						placeholder="etc. AE" />
				</div>				

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
		<div class="col-md-8">

		</div>
	</div>

@endsection
