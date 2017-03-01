<h2>Outlets
	<small>
		<a href="{{ route('dashboard.merchants.outlets.create', $merchant->id) }}" class="btn btn-sm btn-default">
			Add New
		</a>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Transactions Today</th>
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
				@if( $outlet->has_reservation )
					<span class="label label-success">Has reservation</span>
				@else
					<span class="label label-danger">No reservation</span>
				@endif
			</td>
			<td></td>
			<td>
				@if( $outlet->is_open )
					<span class="label label-success">Open</span>
				@else
					<span class="label label-danger">Close</span>
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