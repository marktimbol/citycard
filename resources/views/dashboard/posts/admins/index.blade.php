@extends('layouts.dashboard')

@section('pageTitle', 'Staffs')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Users
			<smal>
				<a href="{{ route('dashboard.users.create') }}" class="btn btn-sm btn-primary">Add New</a>
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
			@forelse( $users as $user )
			<tr>
				<td>
					<a href="{{ route('dashboard.users.show', $user->id) }}">
						{{ $user->name }}
					</a>
				</td>
				<td>{{ $user->email }}</td>
				<td>
					<a href="{{ route('dashboard.users.edit', $user->id) }}"
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
