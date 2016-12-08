@extends('layouts.dashboard')

@section('pageTitle', 'Roles')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Roles
			<smal>
				<a href="{{ route('dashboard.roles.create') }}" class="btn btn-sm btn-primary">Add New</a>
			</smal>
		</h1>
		@include('dashboard._search-form')
	</div>

	<div class="row">
		<div class="col-md-4">
			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.roles.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						value="{{ old('name') }}"
						placeholder="etc. Manage Merchants" />
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
						<th>Name</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $roles as $role )
					<tr>
						<td>
							{{ $role->name }}
						</td>
						<td>
							<a href="#" class="btn btn-sm btn-default">
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
