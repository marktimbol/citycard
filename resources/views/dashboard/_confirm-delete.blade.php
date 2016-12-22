<button class="btn btn-sm btn-danger" data-toggle="modal"
	data-target="#ConfirmDeleteModal"
>
	Delete
</button>	

<div class="modal fade" id="ConfirmDeleteModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4>You sure you want to delete this record?</h4>
			</div>
			<div class="modal-body">
				<p>Once you delete this record, there is no going back. Please be certain.</p>
				<form method="POST" action="{{ $route }}">
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<div class="form-group">
						<button type="submit" class="btn btn-danger">Yes, delete this record</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>