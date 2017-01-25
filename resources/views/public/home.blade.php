@extends('layouts.public')

@section('pageTitle', 'Home')

@section('bodyClass', 'Home')

@section('content')
	
	<main>
		<article class="Flex Flex--center">
			<div class="Column-6 PhoneMockup--container">
				<div class="PhoneMockup">
					<div class="PhoneMockup__slides">
						<img src="/images/screenshots/login-screenshot.jpg" alt="CityCard Login" title="CityCard Login" class="PhoneMockup__slide1" />
						<img src="/images/screenshots/feeds-screenshot.jpg" alt="CityCard Feeds" title="CityCard Feeds" class="PhoneMockup__slide2" />
						<img src="/images/screenshots/outlet-screenshot.jpg" alt="CityCard Outlet View" title="CityCard Outlet View" class="PhoneMockup__slide3" />
					</div>
				</div>
			</div>
			<div class="Column-6 max-350">
				<div id="Authentication"></div>
			</div>					
		</article>	
	</main>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/Authentication.js') }}"></script>
@endsection