@extends('layouts.public')

@section('pageTitle', 'Explore Merchants')

@section('bodyClass', 'Explore--page')

@section('content')
	@include('layouts.public._nav')

	<div class="container max-600">
		<div class="row">
			<div class="col-md-12">
				<div class="Explore--container">
					<div class="Explore__title--container">
						<h1 class="Explore__title">Discover Merchants</h1>
					</div>
					<div class="Explore__content--container">
						@foreach( $outlets as $outlet )
						<div class="Explore__content">
							<div class="Explore__content--outlet">
								<div class="Explore__content--outlet-profile">
									<img src="{{ $outlet->merchant->logo !== null ? getPhotoPath($outlet->merchant->logo) : '/images/tmp/outlet-photo.jpg' }}" alt="{{ $outlet->name }}" title="{{ $outlet->name }}" class="img-responsive img-circle" />
									<div>
										<h3>{{ $outlet->name }}</h3>
										<p>
											<small>
												@forelse($outlet->merchant->subcategories as $subcategory )
													{{ $subcategory->name }},
												@empty

												@endforelse
											</small>
										</p>
									</div>
								</div>
								<button class="btn btn-primary btn-follow">Join</button>
							</div>
							<div class="Explore__content--posts">
								@foreach( $outlet->posts as $post )
									<div class="Explore__content--post">
										<img src="{{ $post->photos->count() > 0 ? getPhotoPath($post->photos->first()->url) : '/images/tmp/outlet-post.jpg' }}" alt="{{ $post->title }}" title="{{ $post->title }}" class="img-responsive" />
									</div>
								@endforeach
							</div>
						</div>
						@endforeach
					</div>
				</div>

				{{ $outlets->links() }}
			</div>
		</div>
	</div>
@endsection