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
						placeholder="etc. United Arab Emirates" />
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
						<th>Country</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $countries as $country )
					<tr>
						<td width="250">
							<a href="#">
								{{ $country->name }}
							</a>
						</td>
						<td>
							<div class="btn-group">
								<a href="{{ route('dashboard.countries.cities.index', $country->id) }}" 
									class="btn btn-sm btn-default"
								>
									Manage Cities
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