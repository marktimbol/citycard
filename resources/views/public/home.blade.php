@extends('layouts.public')

@section('bodyClass', 'Home')

@section('content')
	
	<main>
		<article class="Flex Flex--center">
			<div class="Column-6 PhoneMockup--container">
				<div class="PhoneMockup">
					<div class="PhoneMockup__slides">
						<img src="/images/login-screenshot.png" alt="CityCard Login" title="CityCard Login" class="PhoneMockup__slide1" />
						<img src="/images/iphone-slide2.jpg" alt="" title="" class="PhoneMockup__slide2" />
						<img src="/images/iphone-slide3.jpg" alt="" title="" class="PhoneMockup__slide3" />
					</div>
				</div>
			</div>
			<div class="Column-6 max-350">
				<div id="HomeAuthentication"></div>
			</div>					
		</article>

		<div class="Flex Flex--center Footer--container">
			<div class="Column-6">
				<footer>
					<ul>
						<li><a href="/posts">Deals</a></li>
						<li><a href="/events">Events</a></li>
						<li><a href="/directory">Directory</a></li>
					</ul>
					<ul>
						<li><a href="/about">About Us</a></li>
						<li><a href="#">Merchants</a></li>
						<li><a href="#">FAQs</a></li>
					</ul>
				</footer>
			</div>

			<div class="Column-6">
				<div class="Download-app">
					<ul>
						<li>
							<a href="#">
								<img src="/images/app-store.png" alt="Download on the App Store" title="Download on the App Store" class="img-responsive" />
							</a>
						</li>
						<li>
							<a href="#">
								<img src="/images/google-play.png" alt="Get it on the Google Play" title="Get it on the Google Play" class="img-responsive" />
							</a>
						</li>								
					</ul>
				</div>
			</div>
		</div>

		<footer>
			<ul>
				<li class="copyright text-center">
					&copy; 2017 City Card
				</li>
			</ul>
		</footer>				
	</main>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/HomeAuthentication.js') }}"></script>
@endsection