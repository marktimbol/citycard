@extends('layouts.dashboard')

@section('pageTitle', 'Cities')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">{{ $country->name }} Cities</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.countries.cities.store', $country->id) }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control" />
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
						<th>City</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $cities as $city )
					<tr>
						<td width="250">
							{{ $city->name }} &mdash;
							<small>({{ $city->areas->count() }} Areas)</small>
						</td>
						<td>
							<div class="btn-group">
								<a href="{{ route('dashboard.cities.areas.index', $city->id) }}"
									class="btn btn-sm btn-default"
								>
									Manage Areas
								</a>
								<a href="#"
									class="btn btn-sm btn-default"
								>
									Edit
								</a>
							</div>
						</td>
					</tr>

					@empty
					<tr>
						<td colspan="6">No record yet.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

@endsection
