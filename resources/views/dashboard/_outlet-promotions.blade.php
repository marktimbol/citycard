<h2>Promotions
	<small>
		<button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#AddPromoToOutletModal">
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
		@forelse( $promos as $promo )
		<tr>
			<td>
				<a href="{{ route('dashboard.merchants.promos.show', [$merchant->id, $promo->id]) }}">
					{{ $promo->title }}
				</a>
			</td>
			<td>
				<form method="POST"
					action="{{ route('dashboard.merchants.outlets.promos.destroy', [$merchant->id, $outlet->id, $promo->id]) }}"
				>
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<a href="{{ route('dashboard.merchants.promos.edit', [$merchant->id, $promo->id]) }}" 
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
			<td colspan="6">No promos yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>

<div class="modal fade" id="AddPromoToOutletModal" tabindex="-1" role="dialog">
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
        		<h4 class="modal-title" id="myModalLabel">Add Promo to an Outlet</h4>
			</div>

			<form method="POST" 
				action="{{ route('dashboard.merchants.outlets.promos.store', [$merchant->id, $outlet->id]) }}"
			>
			{{ csrf_field() }}
				<div class="modal-body">
					@foreach( $merchantPromos as $promo )
						<div class="checkbox">
							<label>
								<input type="checkbox" name="promo_ids[]" value="{{ $promo->id }}" /> 
								{{ $promo->title }}
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