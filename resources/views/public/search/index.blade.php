@extends('layouts.public')

@section('pageTitle', 'Search Results')

@section('bodyClass', 'Search')

@section('content')
	@include('layouts.public._nav')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<p></p>
				<div class="btn-group Flex Flex--center Search--filters">
					<a href="/search/{{ $key }}" class="btn btn-default">All</a>
					<a href="/search/newsfeeds/{{ $key }}" class="btn btn-default">Posts</a>
					<a href="/search/deals/{{ $key }}" class="btn btn-default">Offers</a>
					<a href="/search/events/{{ $key }}" class="btn btn-default">Events</a>
					<a href="/search/outlets/{{ $key }}" class="btn btn-default">Places</a>
				</div>
			</div>
		</div>

		<div class="max-600 centered">
			<div id="SearchResults"></div>
		</div>

	</div>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/BlurredImageEffect.js') }}"></script>
	<script src="{{ elixir('js/SearchResults.js') }}"></script>
@endsection