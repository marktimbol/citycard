<div class="modal fade" id="addNewItemForReservation" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4>Create an Item for Reservation</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('dashboard.outlets.for-reservations.store', $outlet->id) }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label class="control-label" for="title">Title</label>
						<input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" />
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>