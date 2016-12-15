@extends('layouts.public')

@section('bodyClass', 'Posts')

@section('content')
	@include('layouts._nav')

	<div class="Posts--container">
		<div class="Posts max-600">
			@foreach( $posts as $post )
				<?php
					$logo = '/images/outlet-avatar.jpg';
					$outlet = $post->outlets->first();

					if( $post->merchant->logo != null ) {
						$logo = getPhotoPath($post->merchant->logo);
					}
				?>
				<div class="Card">
					<div class="Card__header">
						<div class="Flex Flex--center">
							<img src="{{ $logo }}" alt="{{ $outlet->name }}" title="{{ $outlet->name }}" class="img-circle Card__logo" width="30" height="30" />
							<h4 class="Card__title">
								<a href="#">{{ $outlet->name }}</a>
							</h4>
						</div>
						<div>
							<span class="timeago">
								{{ $post->created_at->diffForHumans() }}
							</span>
						</div>
					</div>
					<div class="Card__image">
						<?php
							$featuredImage = 'http://placehold.it/600x500';
							if( $post->photos->count() > 0 ) {
								$featuredImage = getPhotoPath($post->photos->first()->url);
							}
						?>
						<img src="{{ $featuredImage }}" alt="{{ $post->title }}" title="{{ $post->title }}" class="img-responsive" />
					</div>
					<div class="Card__description">
						{!! str_limit($post->desc, 400) !!}
					</div>
				</div>
			@endforeach

			{{ $posts->links() }}
		</div>
	</div>

@endsection