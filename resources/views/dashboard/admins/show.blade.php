@extends('layouts.dashboard')

@section('pageTitle', $admin->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $admin->name }}</h1>
	</div>

	<h3>Merchants ({{ $admin->merchants->count() }})</h3>
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
		</tbody>
	</table>

	{{ $merchants->links() }}

	<h3>Outlets ({{ $admin->outlets->count() }})</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Transactions Today</th>
				<th>Status</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $outlets as $outlet )
			<tr>
				<td>
					<a href="#">
						{{ $outlet->name }}
					</a>
				</td>
				<td></td>
				<td><label class="label label-danger">Close</label></td>
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

	{{ $outlets->links() }}

	<h3>Posts ({{ $admin->posts->count() }})</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Type</th>
				<th>Title</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $posts as $post )
			<tr>
				<td>{{ ucfirst($post->type) }}</td>
				<td>
					<a href="#">
						{{ $post->title }}
					</a>
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

	{{ $posts->links() }}	
@endsection
