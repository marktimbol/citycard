@extends('layouts.dashboard')

@section('pageTitle', 'Areas')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="Heading">
				<h1 class="Heading__title">Areas</h1>
				@include('dashboard._search-form')
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">

			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.cities.areas.store', $city->id) }}">
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
						<th>Area</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $areas as $area )
					<tr>
						<td width="250">
							<a href="#">
								{{ $area->name }}
							</a>
						</td>
						<td>
							<a href="#" 
								class="btn btn-sm btn-default"
							>
								Edit
							</a>
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