@extends('layouts.dashboard')

@section('pageTitle', 'Permissions')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Permissions</h1>
		@include('dashboard._search-form')
	</div>

	<div class="row">
		<div class="col-md-4">
			@include('errors.list')

			<form method="POST" action="{{ route('dashboard.permissions.store') }}">
				{{ csrf_field() }}
				<div class="form-group">
					<label for="name">Name</label>
					<input
						type="text"
						name="name"
						id="name"
						class="form-control"
						value="{{ old('name') }}"
						placeholder="etc. manage_countries, edit_post" />
				</div>

				<div class="form-group">
					<label for="label">Label</label>
					<input
						type="text"
						name="label"
						id="label"
						class="form-control"
						value="{{ old('label') }}"
						placeholder="etc. Manage Countries, Edit Post" />
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
						<th>Label</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					@forelse( $permissions as $permission )
					<tr>
						<td>{{ $permission->name }}</td>
						<td>{{ $permission->label }}</td>
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
