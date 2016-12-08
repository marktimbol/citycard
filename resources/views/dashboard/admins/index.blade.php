@extends('layouts.dashboard')

@section('pageTitle', 'Staffs')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Admins
			<smal>
				<a href="{{ route('dashboard.admins.create') }}" class="btn btn-sm btn-primary">Add New</a>
			</smal>
		</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>eMail</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $admins as $admin )
			<tr>
				<td width="200">
					<a href="{{ route('dashboard.admins.show', $admin->id) }}">
						{{ $admin->name }}
					</a>
				</td>
				<td>{{ $admin->email }}</td>
				<td>
					<button class="btn btn-sm btn-primary">Attach Roles</button>
					<a href="{{ route('dashboard.admins.edit', $admin->id) }}"
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

@endsection
