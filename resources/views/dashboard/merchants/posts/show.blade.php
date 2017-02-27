@extends('layouts.dashboard')

@section('pageTitle', $post->title)

@section('header_styles')
	<link rel="stylesheet" href="{{ elixir('css/mobile.css') }}" />
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.posts.show', $post) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">{{ $post->title }}
			@if( adminCan('edit', $post))
				<small>
					<a href="{{ route('dashboard.merchants.posts.edit', [$merchant->id, $post->id]) }}">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
			@endif
		</h1>
	</div>

	<p>&nbsp;</p>

	<div class="row">
		<div class="col-md-7">
			<ul class="list-group">
				<li class="list-group-item">
					Merchant: 
					<a href="{{ route('dashboard.merchants.show', $post->merchant->id) }}">
						{{ $post->merchant->name }}
					</a>
				</li>
				<li class="list-group-item">
					Outlets:
					@foreach( $post->outlets as $outlet )
						<a href="{{ route('dashboard.merchants.outlets.show', [
							$post->merchant->id,
							$outlet->id
						]) }}"
						>
							{{ $outlet->name }}
						</a>
					@endforeach
				</li>				
			</ul>
			<ul class="list-group">
				<li class="list-group-item">
					Post Type: 
					<a href="#">{{ ucfirst($post->type) }}</a>
				</li>
				@if( $post->type == 'events' )
					<li class="list-group-item">
						Event Date: {{ $post->event_date->toFormattedDateString() }}<br />
						Event Time: {{ $post->event_time }}
					</li>
				@endif
			</ul>

			<ul class="list-group">
				<li class="list-group-item">
					Category: 
					<a href="{{ route('dashboard.categories.posts.index', $post->category->id) }}">
						{{ $post->category->name }}
					</a><br />
					Sub-Categories:
					@foreach( $post->subcategories as $subcategory )
						<label class="label label-success">{{ $subcategory->name }}</label>
					@endforeach
				</li>
			</ul>
			<ul class="list-group">
				<li class="list-group-item">
					Posted By: <a href="{{ route('dashboard.admins.show', $post->creator()->first()->id) }}">
						{{ $post->creator()->first()->name }}
					</a>
				</li>
				@if( $post->isExternal )
					<li class="list-group-item">
						From <a href="{{ $post->sources->first()->pivot->link }}" target="_blank">
							{{ $post->sources->first()->name }}
						</a>
					</li>
				@endif				
			</ul>

			{!! $post->desc !!}

			<p>&nbsp;</p>

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

			<form
				method="POST"
				class="dropzone"
				id="UploadPostPhotos"
				action="{{ route('dashboard.posts.photos.store', $post->id) }}"
			>
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
									@if( $merchant->logo !== '' )
										<img src="{{ getPhotoPath($merchant->logo)}}" alt="" title="" width="60" height="60" class="img-responsive" />
									@else
										<img src="/images/avatar.jpg" alt="" title="" width="60" height="60" class="img-responsive" />
									@endif
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

							<h2>{{ $post->title }}</h2>

							{!! $post->desc !!}

							@if( $post->type != 'notification' )
								<p class="text-center">
									<a href="{{ $post->link }}" class="btn btn-primary" target="_blank">Buy now</a>
								</p>
							@endif
						</div>
					</div>
			    </div>
			    <div class="home"></div>
			    <div class="bottom-bar"></div>
			</div>
		</div>
	</div>

	@if( adminCan('delete', $post) )
		@include('dashboard._delete', [
			'route'	=> route('dashboard.merchants.posts.destroy', [$merchant->id, $post->id])
		])
	@endif
@endsection
