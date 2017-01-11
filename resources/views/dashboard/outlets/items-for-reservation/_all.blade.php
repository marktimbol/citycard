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
			<th>Options</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $items as $item )
		<tr>
			<td>
				{{ $item->title }}
			</td>
			<td>
				@if( $item->options !== null )
					@foreach($item->options as $option)
						<span class="label label-success">{{ $option }}</span>
					@endforeach
				@endif
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