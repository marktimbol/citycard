<h2>Working In
	<small>
		<button 
			class="btn btn-sm btn-primary" 
			data-toggle="modal" 
			data-target="#assignOutletsTo{{$clerk->id}}"
		>
			Assign to Outlet
		</button>		
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>eMail</th>
			<th>Phone</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $outlets as $outlet )
		<tr>
			<td>
				<a href="{{ route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]) }}">
					{{ $outlet->name }}
				</a>
			</td>
			<td>{{ $outlet->email }}</td>
			<td>{{ $outlet->phone }}</td>
			<td>
				<label class="label label-danger">Close</label>
			</td>
			<td>
				<a href="{{ route('dashboard.merchants.outlets.edit', [$merchant->id, $outlet->id]) }}" 
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