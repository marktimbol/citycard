@extends('layouts.dashboard')

@section('pageTitle', 'Rewards')

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Rewards
			<small>
				<a href="{{ route('dashboard.rewards.create') }}" class="btn btn-sm btn-primary">
					Create New
				</a>
			</small>
		</h1>
	</div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Title</th>
				<th>Required Points</th>
				<th>Quantity</th>
				<th>Available in</th>
			</tr>
		</thead>
		<tbody>
			@forelse( $rewards as $reward )
			<tr>
				<td>{{ $reward->title }}</td>
				<td>{{ $reward->required_points }}</td>
				<td>{{ $reward->quantity }}</td>
				<td>
					@foreach( $reward->outlets as $outlet )
						<span class="label label-success">{{ $outlet->name }}</span>
					@endforeach
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="4">No rewards yet.</td>
			</tr>
			@endforelse
		</tbody>
	</table>
@endsection
