@extends('layouts.public')

@section('pageTitle', 'Search Results')

@section('bodyClass', 'Search')

@section('content')
	@include('layouts.public._nav')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p></p>
				<div class="btn-group Flex Flex--center">
					<a href="/search/{{ $key }}" class="btn btn-default">All</a>
					<a href="/search/newsfeeds/{{ $key }}" class="btn btn-default">Posts</a>
					<a href="/search/deals/{{ $key }}" class="btn btn-default">Offers</a>
					<a href="/search/events/{{ $key }}" class="btn btn-default">Events</a>
					<a href="/search/outlets/{{ $key }}" class="btn btn-default">Places</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
		    	<div class="Events row">
		    		<div class="col-md-12">
		    			<h3>Events</h3>
		    		</div>
			    	@forelse( $events as $item )
						<div class="Event Column-4">
							<div class="Event__header">
								<div class="Event__image">
									@if( $item->photos->count() > 0 )
									<?php $photo = $item->photos->first(); ?>
										<a href="/posts/{{$item->slug}}">
											<div class="placeholder" data-large="{{ getPhotoPath($photo->url) }}">
												<img src="/images/blurred-image.jpeg" class="img-small" /> 
												<div class="aspect-ratio-fill"></div> 
											</div>
										</a>
									@else
										<a href="/posts/{{$item->slug}}">
											<div class="placeholder" data-large="/images/event-cover.png">
												<img src="/images/blurred-image.jpeg" class="img-small" /> 
												<div class="aspect-ratio-fill"></div> 
											</div>
										</a>
									@endif
								</div>
							</div>
							<div class="Event__content">
								<div class="Event__date">
									<span class="Event__date--month">
										{{ $item->event_date ? date('M', $item->event_date->timestamp) : '' }}
									</span>
									<span class="Event__date--day">
										{{ $item->event_date ? date('d', $item->event_date->timestamp) : '' }}
									</span>
								</div>
								<div class="Event__info">
									<h3 class="Event__title text-ellipsis">{{ $item->title }}</h3>
									<small>{{ $item->event_time or '09:00 - 18:00' }}</small>
									<p class="text-ellipsis">{{ $item->event_location or 'Dubai, United Arab Emirates' }}</p>
								</div>
							</div>
						</div>
				    @empty
				    @endforelse
		    	</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
		    	<div class="Offers--results row">
		    		<div class="col-md-12">
		    			<h3>Offers</h3>
		    		</div>
			    	@forelse( $deals->chunk(2) as $items )
			    		<div class="Row">
			    			@foreach( $items as $item )			    	
								<div class="Offer Column-6">
									<div class="Card">
										<div class="Card__header">
											<div class="Flex Flex--center">
												<img src="/images/avatar.jpg" 
													alt="{{ $item->merchant->name }}" 
													title="{{ $item->merchant->name }}" 
													class="img-circle Card__logo" 
													width="30" height="30" />
												<h4 class="Card__title">
													<a href="#">{{ $item->merchant->name }}</a>
												</h4>
											</div>
											<div>
												<span class="timeago">
													{{ $item->created_at->diffForHumans() }}
												</span>
											</div>
										</div>
										<div class="Card__image">
											<a href="/posts/{{$item->slug}}">
												<div class="placeholder" data-large="/images/event-cover.png">
													<img src="/images/blurred-image.jpeg" class="img-small" /> 
													<div class="aspect-ratio-fill"></div> 
												</div>
											</a>
										</div>
										<div class="Card__description">
											<h3>
												<a href="/posts/{{$item->slug}}">{{ $item->title }}</a>
											</h3>
											{{-- {{ str_limit($item->desc, 300) }} --}}
										</div>
									</div>
								</div>
							@endforeach
						</div>
				    @empty
				    @endforelse
		    	</div>
			</div>
		</div>		

	</div>
@endsection

@section('footer_scripts')
	<script src="/js/BlurredImageEffect.js"></script>
@endsection