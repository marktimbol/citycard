<h2>Clerks
	<small>
		<a href="{{ route('dashboard.outlets.clerks.create', $outlet->id) }}" class="btn btn-sm btn-default">Add New</a>
		<button class="btn btn-sm btn-default" data-toggle="modal" data-target="#SelectExistingClerks">
			Select from existing
		</button>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Access Control</th>
			<th>Status</th>
			<th>Last Logged-in</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $outletClerks as $clerk )
		<tr>
			<td width="200">
				<a href="{{ route('dashboard.merchants.clerks.show', [$merchant->id, $clerk->id]) }}">
					{{ sprintf('%s %s', $clerk->first_name, $clerk->last_name) }}
				</a>
			</td>
			<td></td>
			<td>
				<label class="label label-danger">Offline</label>
			</td>
			<td>

			</td>
			<td>
				<form method="POST" action="{{ route('dashboard.outlets.clerks.destroy', [$outlet->id, $clerk->id]) }}">
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<button type="submit" class="btn btn-link">
						<i class="fa fa-times-rectangle"></i>
					</button>
				</form>
			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>