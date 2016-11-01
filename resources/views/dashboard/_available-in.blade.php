<h2>Available In
	<small>
		<button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#AddOutletToAPromoModal">
			Add New
		</button>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>eMail</th>
			<th>Phone</th>
			<th>Country</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $outlets as $outlet )
		<tr>
			<td width="250">
				<a href="{{ route('dashboard.merchants.outlets.show', [$merchant->id, $outlet->id]) }}">
					{{ $outlet->name }}
				</a>
			</td>
			<td>{{ $outlet->email }}</td>
			<td>{{ $outlet->phone }}</td>
			<td>{{ sprintf('%s, %s', $outlet->city, $outlet->country) }}</td>
			<td>
				<form method="POST"
					action="{{ route('dashboard.merchants.promos.outlets.destroy', [
						$merchant->id, 
						$promo->id, 
						$outlet->id
					]) }}"
				>
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<a href="{{ route('dashboard.merchants.outlets.edit', [$merchant->id, $outlet->id]) }}" 
						class="btn btn-sm btn-default"
					>
						Edit
					</a>
					<button type="submit" class="btn btn-sm btn-danger">Delete</button>
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

<div class="modal fade" id="AddOutletToAPromoModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" 
					class="close" 
					data-dismiss="modal" 
					aria-label="Close"
				>
					<span aria-hidden="true">&times;</span>
				</button>
        		<h4 class="modal-title" id="myModalLabel">Add Outlet to a Promo</h4>
			</div>

			<form 
				method="POST" 
				action="{{ route('dashboard.merchants.promos.outlets.store', [$merchant->id, $promo->id]) }}"
			>
			{{ csrf_field() }}
				<div class="modal-body">
					@foreach( $merchantOutlets as $outlet )
						<div class="checkbox">
							<label>
								<input type="checkbox" name="outlet_ids[]" value="{{ $outlet->id }}" /> 
								{{ $outlet->name }}
							</label>
						</div>
					@endforeach
				</div>
				<div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        <button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>