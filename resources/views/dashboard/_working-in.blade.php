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
			<th>Outlet</th>
			<th>eMail</th>
			<th>Phone</th>
			<th>Status</th>
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
				@if( $outlet->is_open )
					<label class="label label-success">Open</label>
				@else
					<label class="label label-danger">Close</label>
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


<div 
	class="modal fade" 
	id="assignOutletsTo{{$clerk->id}}" 
	tabindex="-1" 
	role="dialog"
>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Assign {{ $clerk->name }} to Outlet</h4>
			</div>
				<div class="modal-body">
					<form method="POST" action="{{ route('dashboard.clerks.outlets.store', $clerk->id) }}">
						{{ csrf_field() }}									
						@foreach( $merchant->outlets as $outlet )
							<div class="checkbox">
								<label>
									<input type="checkbox" 
										name="outlets[]" 
										value="{{ $outlet->id }}" 
										{{ $clerk->outlets->contains($outlet) ? 'checked' : '' }}
										/>
									{{ $outlet->name }}
								</label>
							</div>
						@endforeach

						<button type="submit" class="btn btn-sm btn-primary">
							Assign
						</button>											
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
						Close
					</button>
				</div>
			</form>
		</div>
	</div>
</div>	
