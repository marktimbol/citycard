@extends('layouts.dashboard')

@section('pageTitle', sprintf("%s Outlets", $merchant->name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Outlets
			<smal>
				<a href="{{ route('dashboard.merchants.outlets.create', $merchant->id) }}" 
					class="btn btn-sm btn-primary"
				>
					Add New
				</a>
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
			@forelse( $outlets as $outlet )
			<tr>
				<td width="250">
					<a href="{{ route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]) }}">
						{{ $outlet->name }}
					</a>
				</td>
				<td>{{ $outlet->email }}</td>
				<td>{{ $outlet->phone }}</td>
				<td>{{ sprintf('%s, %s', $outlet->city, $outlet->country) }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.outlets.edit', [$merchant->id, $outlet->id]) }}" 
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