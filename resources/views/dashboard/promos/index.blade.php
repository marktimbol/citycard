@extends('layouts.dashboard')

@section('pageTitle', sprintf('%s Promos', $merchant->name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Promos
			<smal>
				<a href="{{ route('dashboard.merchants.promos.create', $merchant->id) }}" 
					class="btn btn-primary"
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
				<th>&nbsp;</th>
				<th>Title</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $promos as $key => $promo )
			<tr>
				<td>{{ $key + 1 }}</td>
				<td>
					<a href="{{ route('dashboard.merchants.promos.show', [$merchant->id, $promo->id]) }}">
						{{ $promo->title }}
					</a>
				</td>
				<td>
					<a href="{{ route('dashboard.merchants.promos.edit', [$merchant->id, $promo->id]) }}" 
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