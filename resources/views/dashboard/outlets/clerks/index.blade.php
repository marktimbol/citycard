@extends('layouts.dashboard')

@section('pageTitle', 'Clerks')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Clerks
			<smal>
				<a href="{{ route('dashboard.merchants.clerks.create', $merchant->id) }}" class="btn btn-sm btn-primary">Add New</a>
			</smal>
		</h1>
		@include('dashboard._search-form')
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>eMail</th>
				<th>Phone</th>
				<th>Country</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $clerks as $clerk )
			<tr>
				<td>
					<a href="{{ route('dashboard.merchants.clerks.show', [$merchant->id, $clerk->id]) }}">
						{{ sprintf('%s %s', $clerk->first_name, $clerk->last_name) }}
					</a>
				</td>
				<td>{{ $clerk->email }}</td>
				<td>{{ $clerk->phone }}</td>
				<td>{{ sprintf('%s, %s', $clerk->city, $clerk->country) }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.clerks.edit', [$merchant->id, $clerk->id]) }}" 
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