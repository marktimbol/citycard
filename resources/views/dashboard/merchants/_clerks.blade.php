<h2>Clerks
	<small>
		<a href="{{ route('dashboard.merchants.clerks.create', $merchant->id) }}" class="btn btn-sm btn-default">
			Add New
		</a>
		<button class="btn btn-sm btn-default" data-toggle="modal" data-target="#SelectExistingClerks">
			Select from existing
		</button>		
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Working In</th>
			<th>Access Control</th>
			<th>Status</th>
			<th>Last Logged-in</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $clerks as $clerk )
		<?php $clerk->load('outlets'); ?>
		<tr>
			<td>
				<a href="{{ route('dashboard.merchants.clerks.show', [$merchant->id, $clerk->id]) }}">
					{{ sprintf('%s %s', $clerk->first_name, $clerk->last_name) }}
				</a>
			</td>
			<td>
				@foreach( $clerk->outlets as $outlet )
					<span class="label label-success">
						<a href="{{ route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]) }}">
							{{ $outlet->name }}
						</a>
					</span>
				@endforeach
			</td>
			<td>
				<span class="label label-success">
					As Clerk
				</span>
			</td>			
			<td>
				<span class="label label-danger">Offline</span>
			</td>
			<td>{{ \Carbon\Carbon::yesterday()->diffForHumans() }}</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>

