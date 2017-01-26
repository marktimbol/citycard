@extends('layouts.public')

@section('pageTitle', 'Post')

@section('bodyClass', 'Posts')

@section('content')
	@include('layouts.public._nav')
	<div class="Posts--container">
		<div class="Post max-600">
			<div class="Card">
				<div class="Card__header">
					<div class="Flex Flex--center">
						<img src="{{ $post->merchant->logo != null ? getPhotoPath($post->merchant->logo) : 'http://placehold.it/28x28' }}" alt="{{ $post->merchant->name }}" title="{{ $post->merchant->name }}" class="img-circle Card__logo" width="30" height="30" />
						<h4 class="Card__title">
							<a href="#">{{ $post->merchant->name }}</a>
						</h4>
					</div>
					<div>
						<span class="timeago">
							{{ $post->created_at->diffForHumans() }}
						</span>
					</div>
				</div>
				<div class="Card__image">
					<div
						class="placeholder"
						data-large="{{ getPhotoPath($post->photos()->first()->url) }}"
					>
							<img
								src="https://cdn-images-1.medium.com/freeze/max/30/1*XtTMZ5cZ2KWWVFATIr3dpQ.png?q=20"
								class="img-small" /> 
							<div class="aspect-ratio-fill"></div> 
					</div>				
				</div>
				<div class="Card__description">
					<h3>{{ $post->title }}</h3>
					{!! $post->desc !!}
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')

@endsection