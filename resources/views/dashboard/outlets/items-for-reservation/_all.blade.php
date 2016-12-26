<h2>Items For Resevation
	<small>
		<button class="btn btn-sm btn-default" data-toggle="modal" data-target="#addNewItemForReservation">
			Add New
		</button>		
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Title</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $items as $item )
		<tr>
			<td>
				{{ $item->title }}
			</td>
			<td>

			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No items yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>

@include('dashboard.outlets.items-for-reservation._create')