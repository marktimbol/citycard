<p>&nbsp;</p>

<div class="panel panel-danger">
	<div class="panel-heading">Danger Zone</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-10">
				<p>Once you delete this record, there is no going back. Please be certain.</p>
			</div>
			<div class="col-md-2">
				<form method="POST" action="{{ $route }}">
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<div class="form-group">
						<button type="submit" class="btn btn-danger">Delete</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>