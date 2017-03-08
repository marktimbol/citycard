@extends('layouts.merchant')

@section('content')
	<h2 class="Heading__title">Dashboard</h2>

	<h3>Merchants</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="100">
					<div class="Flex Flex--center">
						<img src="/images/no-image.jpg" 
							alt="{{ $clerk->merchant->name }}" 
							title="{{ $clerk->merchant->name }}"
							width="32" height="32"
							class="img-circle" />
					</div>
				</td>
				<td>
					<a href="{{ route('clerk.merchants.show', $clerk->merchant->id) }}">
						{{ $clerk->merchant->name }}
					</a>
				</td>
				<td>{{ $clerk->merchant->email }}</td>
				<td>{{ $clerk->merchant->phone }}</td>
			</tr>
		</tbody>
	</table>

	<h3>Outlets</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $clerk->outlets as $outlet )
			<tr>
				<td width="100">
					<div class="Flex Flex--center">
						<img src="/images/no-image.jpg" 
							alt="{{ $outlet->name }}" 
							title="{{ $outlet->name }}"
							width="32" height="32"
							class="img-circle" />
					</div>
				</td>
				<td>
					<a href="{{ route('clerk.outlets.show', $outlet->id) }}">
						{{ $outlet->name }}
					</a>
				</td>
				<td>{{ $outlet->email }}</td>
				<td>{{ $outlet->phone }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<h3>Clerks</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>Last logged-in</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			@foreach( $clerk->merchant->clerks as $clerk )
			<tr>
				<td width="100">
					<div class="Flex Flex--center">
						<img src="/images/no-image.jpg" 
							alt="{{ $clerk->name }}" 
							title="{{ $clerk->name }}"
							width="32" height="32"
							class="img-circle" />
					</div>
				</td>
				<td>
					<a href="{{ route('clerk.clerks.show', $clerk->id) }}">
						{{ $clerk->fullName() }}
					</a>
				</td>
				<td>{{ $clerk->email }}</td>
				<td>{{ $clerk->phone }}</td>
				<td>{{ $clerk->last_logged_in ? $clerk->last_logged_in->diffForHumans() : '' }}</td>
				<td>
					@if( $clerk->is_online )
						<label class="label label-success">Online</label>
					@else
						<label class="label label-danger">Offline</label>
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>		
@endsection