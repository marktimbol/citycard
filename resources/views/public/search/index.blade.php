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

		<div class="Search--results">
			<div class="Search__events">
				<div class="row">
					<div class="col-md-12">
						@foreach( $events as $event )
							<div class="Card">
								<div class="Card__header">
									<div class="Flex Flex--center">
										<img src="/images/avatar.jpg" alt="{{ $event->title }}" title="{{ $event->title }}" class="img-circle Card__logo" width="30" height="30" />
										<h4 class="Card__title">
											<a href="#">{{ $event->title }}</a>
										</h4>
									</div>
									<div>
										<span class="timeago">
											{{ $item->created_at->diffForHumans() }}
										</span>
									</div>
								</div>
								<div class="Card__image">
									<a href="#">
										<img src="/images/event-cover.png" alt="{{ $item->title }}" title="{{ $item->title }}" class="img-responsive" />
									</a>
								</div>
								<div class="Card__description">
									<h3><a href="#">{{ $item->title }}</a></h3>
									{!! $item->desc !!}
								</div>
							</div>
						@endforeach						
					</div>
				</div>
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