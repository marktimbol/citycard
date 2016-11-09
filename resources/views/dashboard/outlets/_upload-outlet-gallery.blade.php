<div class="modal fade" id="UploadOutletGallery" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4>Upload Shop Front Photos</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row OutletPhotos">
							@forelse( $outlet->photos as $photo )
								<?php 
									$path = getPhotoPath($photo->url);
								?>
								<div class="col-md-3 OutletPhoto has-delete-icon">
									<img src="{{ $path }}" alt="{{ $outlet->name }}" title="{{ $outlet->name }}" class="img-responsive" />
									<form method="POST" action="{{ route('dashboard.outlets.photos.destroy', [$outlet->id, $photo->id]) }}">
										{{ csrf_field() }}
										{!! method_field('DELETE') !!}
										<button type="submit" class="btn btn-sm btn-danger">
											<i class="fa fa-remove"></i>
										</button>
									</form>
								</div>
							@empty
							@endforelse
						</div>
					</div>
				</div>
				<form 
					method="POST" 
					class="dropzone"
					id="UploadOutletGallery"
					action="{{ route('dashboard.outlets.photos.store', $outlet->id) }}" 
				>
					{{ csrf_field() }}
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>