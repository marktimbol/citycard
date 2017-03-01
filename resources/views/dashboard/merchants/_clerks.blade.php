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
			<th>Last Logged-in</th>
			<th>&nbsp;</th>
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
			<td>{{ $clerk->last_logged_in->diffForHumans() }}</td>
			<td>
				@if( $clerk->is_online )
					<label class="label label-success">Online</label>
				@else
					<label class="label label-danger">Offline</label>
				@endif
			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>

