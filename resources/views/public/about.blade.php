@extends('layouts.public')

@section('bodyClass', 'About')

@section('content')
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					{{ config('app.name') }}
				</a>
	    	</div>
	  	</div>
	</nav>

	<div class="About__content">
		<div class="About__header--container">
			<div class="container Flex Flex--center">
				<div class="row">
					<div class="col-md-12">
						<div class="About__header Flex Flex--center Flex--column">
							<h1 class="About__title">Welcome to CityCard</h1>
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

		<div class="About__app--container">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="About__app Flex Flex--center">
							<div class="Column-6">
								<h2 class="About__subtitle">About Our App</h2>
								<p class="lead">
									Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas faucibus, nunc consequat cursus cursus, urna diam euismod arcu, et iaculis turpis sapien quis velit. Nam maximus lectus eu consequat pulvinar. Duis porttitor euismod.
								</p>
								<p class="lead">
									Donec nulla est, rutrum in dui eget, ultricies mollis leo. Duis non diam sodales, tristique enim vitae, lacinia mauris.
								</p>
								<p>&nbsp;</p>
								<p>
									<button class="btn btn-lg btn-primary">Watch demo</button>
								</p>
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
									<i class="fa fa-html5 fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">User Friendly</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
									</p>
								</div>
							</div>
							<div class="Feature Column-4">
								<div class="Column-1">
									<i class="fa fa-desktop fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">Fully Responsive</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
									</p>
								</div>
							</div>	
							<div class="Feature Column-4">
								<div class="Column-1">
									<i class="fa fa-diamond fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">24/7 Support</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
									</p>
								</div>
							</div>													
						</div>

						<div class="Features">
							<div class="Feature Column-4">
								<div class="Column-1">
									<i class="fa fa-heart-o fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">Notifications</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
									</p>
								</div>
							</div>

							<div class="Feature Column-4">
								<div class="Column-1">
									<i class="fa fa-headphones fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">Accessibility</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
									</p>
								</div>
							</div>

							<div class="Feature Column-4">
								<div class="Column-1">
									<i class="fa fa-wechat fa-2x"></i>
								</div>
								<div class="Column-9">
									<h3 class="Feature__title">Chat with Clerks</h3>
									<p>
										Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
										tempor incididunt.
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
									<h3 class="About__subtitle">Dubai, United Arab Emirates</h3>
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
	</div>

@endsection