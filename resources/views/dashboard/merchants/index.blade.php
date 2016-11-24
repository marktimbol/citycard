@extends('layouts.dashboard')

@section('pageTitle', 'Merchants')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Merchants
			<smal>
				<a href="#" class="btn btn-sm btn-default">Import Merchants</a>
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
				<?php
					$area = $merchant->areas->first();
				?>
			<tr>
				<td>
					<a href="{{ route('dashboard.merchants.show', $merchant->id) }}">
						{{ $merchant->name }}
					</a>
				</td>
				<td>{{ $merchant->email }}</td>
				<td>{{ $merchant->phone }}</td>
				<td>{{ sprintf('%s, %s', $area->city->name, $area->city->country->name) }}</td>
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

			<tr>
				<td colspan="6">
					<a href="{{ route('dashboard.merchants.create') }}" class="btn btn-sm btn-primary">Add New</a>
				</td>
			</tr>
		</tbody>
	</table>

@endsection
