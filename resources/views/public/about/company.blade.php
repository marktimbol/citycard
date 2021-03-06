@extends('layouts.public')

@section('pageTitle', 'About CityCard')

@section('bodyClass', 'About')

@section('content')
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					<img src="/images/logo.svg" alt="CityCard" title="CityCard" class="img-responsive" width="175" height="51" />
				</a>
	    	</div>
	  	</div>
	</nav>

	<div class="Company__header--container">
		<div class="container Flex Flex--center">
			<div class="row">
				<div class="col-md-12">
					<div class="Company__header Flex Flex--center Flex--column">
						<h1 class="Company__title">Welcome to CityCard</h1>
						<p class="lead">
							Download The App On App Store And Play Store
						</p>	

						<div class="Action__buttons">
							<a href="#" class="btn btn-lg btn-primary">Download Now</a>
							<a href="#" class="btn btn-lg btn-default">Read more</a>
						</div>
					</div>	
				</div>
			</div>
		</div>
	</div>

	<div class="Company__app--container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="Company__app Flex Flex--center">
						<div class="Column-6">
							<h2 class="Company__subtitle">About Our App</h2>
				
							<p>
								CityCard is a unique loyalty program designed to bring more pleasure to your retail and dining experience. Avid shoppers will get the opportunity to earn points from a wide variety of outlets and redeem rewards in the form of cash back, discounts and free merchandise. CityCard has partnered up with a diverse range of outlets to ensure that customers are rewarded, no matter their preference.
							</p>
							<p>&nbsp;</p>
						</div>
						<div class="Column-6">
							<img src="/images/home-phones-mockup.png" 
								alt="" 
								title="" 
								class="img-responsive" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="Features--container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2 class="Features__title">Features</h2>
					<div class="Features">
						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-newspaper-o fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Newsfeed</h3>
								<p>
									We provide merchants with a newsfeed system enabling each individual outlet to broadcast their latest news, collections, special offers and in-store events. 
								</p>
							</div>
						</div>
						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-address-book-o fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Directory</h3>
								<p>
									We offer shoppers a comprehensive directory of merchants, which will include up-to-date store information. 
								</p>
							</div>
						</div>	
						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-comments-o fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Messaging</h3>
								<p>
									Customers can communicate directly with merchants through our centralized messaging platform.
								</p>
							</div>
						</div>													
					</div>

					<div class="Features">
						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-photo fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Shopfront</h3>
								<p>
									Customers can view shopfronts and displays in real-time, uploaded by the merchants.
								</p>
							</div>
						</div>

						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-calendar-check-o fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Resevations</h3>
								<p>
									Shoppers can reserve items directly from their store of choice.
								</p>
							</div>
						</div>

						<div class="Feature Column-4">
							<div class="Column-1">
								<i class="fa fa-gift fa-2x"></i>
							</div>
							<div class="Column-9">
								<h3 class="Feature__title">Rewards</h3>
								<p>
									Earn rewards points and redeem as cash back, offers or free merchandise from any participating outlet.
								</p>
							</div>
						</div>														
					</div>						
				</div>
			</div>
		</div>
	</div>

	<div class="Statistics--container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="Statistics Flex">
						<div class="Statistic Column-3">
							<h4 class="Statistic__number">100M</h4>
							<p class="Statistic__label">Monthly active users</p>
						</div>

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">70B</h4>
							<p class="Statistic__label">Messages daily</p>
						</div>	

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">15B</h4>
							<p class="Statistic__label">Likes daily</p>
						</div>

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">80%</h4>
							<p class="Statistic__label">Mobile users</p>
						</div>		
					</div>
					<div class="Statistics Flex">
						<div class="Statistic Column-3">
							<h4 class="Statistic__number">3</h4>
							<p class="Statistic__label">Most visited site in the world*</p>
						</div>

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">50+</h4>
							<p class="Statistic__label">Supported Languages</p>
						</div>	

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">30</h4>
							<p class="Statistic__label">Developers</p>
						</div>

						<div class="Statistic Column-3">
							<h4 class="Statistic__number">20</h4>
							<p class="Statistic__label">Designers</p>
						</div>		
					</div>							
				</div>				
			</div>
		</div>
	</div>

	<div class="Headquarters--container">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="Headquarters Flex Flex--center">
						<div class="Column-6">
							<div class="Headquarters__image">
								<img src="/images/dubai-skyline.svg" alt="CityCard Dubai UAE Office" title="CityCard Dubai UAE Office" class="img-responsive" />
							</div>
						</div>
						<div class="Column-6">
							<div class="Headquarters__content">
								<h3 class="Company__subtitle">Dubai, United Arab Emirates</h3>
								<p class="lead">
									Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
									tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
									quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	

@endsection