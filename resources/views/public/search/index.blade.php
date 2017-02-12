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
			    	@forelse( $events as $event )
						<div class="Event Column-4">
							<div class="Event__header">
								<div class="Event__image">
									@if( $event->photos->count() > 0 )
									<?php $photo = $event->photos->first(); ?>
										<a href="/posts/{{$event->slug}}">
											<div
												class="placeholder"
												data-large="{{ getPhotoPath($photo->url) }}"
											>
													<img
														src="/images/blurred-image.jpeg"
														class="img-small" /> 
													<div class="aspect-ratio-fill"></div> 
											</div>
										</a>
									@else
										<a href="/posts/{{$event->slug}}">
											<div
												class="placeholder"
												data-large="/images/event-cover.png"
											>
													<img
														src="/images/blurred-image.jpeg"
														class="img-small" /> 
													<div class="aspect-ratio-fill"></div> 
											</div>
										</a>
									@endif
								</div>
							</div>
							<div class="Event__content">
								<div class="Event__date">
									<span class="Event__date--month">
										{{ $event->event_date ? date('M', $event->event_date->timestamp) : '' }}
									</span>
									<span class="Event__date--day">
										{{ $event->event_date ? date('d', $event->event_date->timestamp) : '' }}
									</span>
								</div>
								<div class="Event__info">
									<h3 class="Event__title text-ellipsis">{{ $event->title }}</h3>
									<small>{{ $event->event_time or '09:00 - 18:00' }}</small>
									<p class="text-ellipsis">{{ $event->event_location or 'Dubai, United Arab Emirates' }}</p>
								</div>
							</div>
						</div>
				    @empty
				    @endforelse
		    	</div>
			</div>
		</div>

		<div class="_Search--results">
			<div class="Search__events">
			
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">

			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')

@endsection