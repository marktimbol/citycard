@extends('layouts.dashboard')

@section('pageTitle', $post->title)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $post->title }}
			<small>
				<a href="{{ route('dashboard.merchants.posts.edit', [$merchant->id, $post->id]) }}">
					<i class="fa fa-pencil"></i>
				</a>
			</small>
		</h1>
		@include('dashboard._go-back')
	</div>

	<p>&nbsp;</p>

	<div class="row">
		<div class="col-md-7">
			<ul class="list-group">
				<li class="list-group-item">
					Price: AED {{ $post->price }}
				</li>
				<li class="list-group-item">
					Payment Option: {{ $post->payment_option == 'both' ? 'Cashback & Points' : $post->payment_option }}
				</li>
				<li class="list-group-item">
					Points will earn: {{ $post->points }}
				</li>
			</ul>
			
			{!! $post->desc !!}

			<p>&nbsp;</p>
			
			<p>External Link: <a href="{{ $post->link }}" target="_blank">{{ $post->link }}</a></p>

			<h3>Upload Photos</h3>

			@forelse( $photos->chunk(4) as $chunks )
				<div class="row">
					@foreach($chunks as $photo )
						<div class="col-md-3">
							<img src="{{ getPhotoPath($photo->url) }}" alt="" title="" class="img-responsive" />
						</div>
					@endforeach
				</div>
			@empty

			@endforelse

			<form method="POST" class="dropzone" action="{{ route('dashboard.posts.photos.store', $post->id) }}">
				{{ csrf_field() }}
			</form>	


		</div>
		<div class="col-md-5">
			<div class="Mobile">
				<div class="Mobile--container">
					<div class="Mobile__header">
						<div class="Mobile__Outlet--container">
							<div class="Flex">
								<div class="Mobile__Outlet__image">
									<img src="http://placehold.it/48x48" alt="" title="" class="img-responsive" />
								</div>
								<div class="Mobile__Outlet__info">
									<h4>{{ $post->outlets()->first()->name }}</h4>
									<small>Open now</small><br /> 
									<small>{{ $post->created_at->diffForHumans() }}</small>
								</div>
							</div>
						</div>
					</div>
					<div class="Mobile__content">
						@if( count($post->photos) > 0 )
							<?php $photo = $post->photos()->first(); ?>
							<img src="{{ getPhotoPath($photo->url) }}" alt="" title="" class="img-responsive" />
						@else
							<img src="http://placehold.it/700x600" alt="" title="" class="img-responsive" />
						@endif

						{!! $post->desc !!}
					</div>
				</div>
			</div>
		</div>
	</div>
	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.posts.destroy', [$merchant->id, $post->id])
	])
@endsection