@extends('layouts.dashboard')

@section('pageTitle', 'Clerks')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Clerks</h1>
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
			@forelse( $clerks as $clerk )
			<tr>
				<td>
					<a href="{{ route('dashboard.merchants.clerks.show', [$clerk->merchant->id, $clerk->id]) }}"
					>
						{{ $clerk->fullName() }}
					</a>
				</td>
				<td>{{ $clerk->email }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.clerks.edit', [$clerk->merchant->id, $clerk->id]) }}" 
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