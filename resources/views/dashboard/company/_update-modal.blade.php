<div class="modal-dialog" role="document">
	<div class="modal-content">
  		<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    		<h4 class="modal-title">{{ $title }}</h4>
  		</div>
    	<div class="modal-body">
			<form method="POST" action="{{ $route }}">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
				<div class="form-group">
					<textarea name="info" id="editor{{$editor}}">
						{{ $content }}
					</textarea>
				</div>	
				<div class="form-group">
					<button class="btn btn-primary">Update</button>
				</div>											
			</form>
		</div>
  		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  		</div>
	</div>
	</div>
