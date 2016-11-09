<div class="modal fade" id="SelectExistingClerks" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4>Select existing Clerks</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('dashboard.outlets.clerks.store', $outlet->id) }}">
					{{ csrf_field() }}
					@if( count($merchantClerks) > 0 )
						@foreach( $merchantClerks as $clerk )
							<div class="checkbox">
								<label>
									<input type="checkbox" name="clerk_ids[]" id="clerk_id" value="{{ $clerk->id }}" /> {{ $clerk->fullName() }}
								</label>
							</div>
						@endforeach
						<div class="form-group">
							<button type="submit" class="btn btn-primary">Assign clerks</button>
						</div>
					@else
						<p>No available clerks.</p>
					@endif
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>