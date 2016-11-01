@extends('layouts.dashboard')

@section('pageTitle', 'Merchants')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Merchants
			<smal>
				<a href="{{ route('dashboard.merchants.create') }}" class="btn btn-primary">Add New</a>
				<a href="#" class="btn btn-default">Import Merchants</a>
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
			@forelse( $merchants as $merchant )
			<tr>
				<td width="250">
					<a href="{{ route('dashboard.merchants.show', $merchant->id) }}">
						{{ $merchant->name }}
					</a>
				</td>
				<td>{{ $merchant->email }}</td>
				<td>{{ $merchant->phone }}</td>
				<td>{{ sprintf('%s, %s', $merchant->city, $merchant->country) }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.edit', $merchant->id) }}" 
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