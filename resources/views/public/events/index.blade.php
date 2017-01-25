@extends('layouts.public')

@section('pageTitle', 'Events')

@section('content')
	@include('layouts.public._nav')

	<div class="Events--container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				    @foreach( $events as $date => $eventsPerDate )
				    	<h3 class="Events__header">{{ $date }}</h3>
				    	<div class="Events row">
					    	@foreach( $eventsPerDate as $event )
								<div class="Event Column-4">
									<div class="Event__header">
										<div class="Event__image">
											@if( $event->photos->count() > 0 )
											<?php $photo = $event->photos->first(); ?>
												<a href="/posts/{{$event->slug}}">
													<img src="{{ getPhotoPath($photo->url) }}"
														alt="{{ $event->title }}" 
														title="{{ $event->title }}" 
														class="img-responsive" />
												</a>
											@else
												<a href="/posts/{{$event->slug}}">
													<img src="/images/event-cover.png" 
														alt="{{ $event->title }}" 
														title="{{ $event->title }}" 
														class="img-responsive" />
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
					    	@endforeach
				    	</div>
				    @endforeach		
				</div>
			</div>
		</div>
	</div>
	
@endsection