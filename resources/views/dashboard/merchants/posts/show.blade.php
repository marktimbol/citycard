@extends('layouts.dashboard')

@section('pageTitle', $post->title)

@section('header_styles')
	<link rel="stylesheet" href="{{ elixir('css/mobile.css') }}" />
@endsection

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
						<div class="col-md-3 col-xs-3">
							<div class="has-delete-icon">
								<img src="{{ getPhotoPath($photo->url) }}" alt="" title="" class="img-responsive" />
								<form method="POST" action="{{ route('dashboard.posts.photos.destroy', [$post->id, $photo->id]) }}">
									{{ csrf_field() }}
									{{ method_field('DELETE') }}
									<button type="submit" class="btn btn-sm btn-danger">
										<i class="fa fa-remove"></i>
									</button>
								</form>
							</div>
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
			<div class="marvel-device iphone5s silver">
			    <div class="top-bar"></div>
			    <div class="sleep"></div>
			    <div class="volume"></div>
			    <div class="camera"></div>
			    <div class="sensor"></div>
			    <div class="speaker"></div>
			    <div class="screen">
					<div class="Mobile">
						<div class="Mobile__header">
							<div class="Flex">
								<div class="Mobile__Outlet__image">
									<img src="/images/avatar.jpg" alt="" title="" width="60" height="60" class="img-responsive" />
								</div>
								<div class="Mobile__Outlet__info">
									<h4>{{ $post->outlets()->first()->name }}</h4>
									<small>Open now</small><br /> 
									<small>{{ $post->created_at->diffForHumans() }}</small>
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
			    <div class="home"></div>
			    <div class="bottom-bar"></div>
			</div>
		</div>
	</div>
	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.posts.destroy', [$merchant->id, $post->id])
	])
@endsection