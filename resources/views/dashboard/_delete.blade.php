<p>&nbsp;</p>

<div class="panel panel-danger">
	<div class="panel-heading">Danger Zone</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-10">
				<p>Once you delete this record, there is no going back. Please be certain.</p>
			</div>
			<div class="col-md-2">
				@include('dashboard._confirm-delete', [
					'route'	=> $route
				])
			</div>
		</div>
	</div>
</div>