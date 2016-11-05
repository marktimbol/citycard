<h2>Clerks
	<small>
		<a href="#" class="btn btn-sm btn-default">Add New</a>
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
		@forelse( $clerks as $clerk )
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
				<a href="{{ route('dashboard.merchants.clerks.edit', [$merchant->id, $clerk->id]) }}" 
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