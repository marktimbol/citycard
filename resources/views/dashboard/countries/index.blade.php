@extends('layouts.dashboard')

@section('pageTitle', 'Countries')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Countries</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.countries.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						value="{{ old('name') }}"
						placeholder="etc. United Arab Emirates" />
				</div>

				<div class="form-group">
					<label for="iso_code">ISO Code</label>
					<input
						type="text"
						name="iso_code"
						id="iso_code"
						class="form-control"
						value="{{ old('iso_code') }}"
						placeholder="etc. AE" />
				</div>						

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
		</div>
		<div class="col-md-8">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>ISO Code</th>
						<th>Country</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $countries as $country )
					<tr>
						<td>
							{{ $country->iso_code }}
						</td>					
						<td width="250">
							{{ $country->name }} &mdash;
							<small>({{ $country->cities->count() }} Cities)</small>
						</td>
						<td>
							<div class="btn-group">
								<a href="{{ route('dashboard.countries.cities.index', $country->id) }}"
									class="btn btn-sm btn-default"
								>
									Manage Cities
								</a>
								<a href="{{ route('dashboard.countries.edit', $country->id) }}"
									class="btn btn-sm btn-default"
								>
									Edit
								</a>
							</div>
						</td>
					</tr>

					@empty
					<tr>
						<td colspan="3">No record yet.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

@endsection
