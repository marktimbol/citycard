@extends('layouts.public')

@section('bodyClass', 'Home')

@section('content')
	<main>
		<article class="Article__content Flex Flex--center">
			<div class="Column-6 PhoneMockup--container">
				<div class="PhoneMockup"></div>
			</div>
			<div class="Column-6 Authentication--container">
				<div class="Authentication">
					<div class="Box Registration">
						<h1 class="text-center">City Card</h1>
						<p class="lead text-center">
							Sign up to see news, deals, and events from places around you.
						</p>
						<div class="Registration__form">
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-primary"><i class="fa fa-facebook-official"></i> Login with Facebook</button>
							</div>		

							<div class="or--divider">
								<div class="or__line"></div>
								<p class="or__text">or</p>
								<div class="or__line"></div>
							</div>

							<form method="POST">
								{{ csrf_field() }}
								<div class="form-group">
									<input type="text" name="email" class="form-control" placeholder="Mobile Number or Email" />
								</div>
								<div class="form-group">
									<input type="text" name="name" class="form-control" placeholder="Full Name" />
								</div>	
								<div class="form-group">
									<input type="password" name="password" class="form-control" placeholder="Password" />
								</div>	
								<div class="form-group">
									<input type="password" name="password_confirmation" class="form-control" placeholder="Repeat Password" />
								</div>	
								<div class="form-group">
									<button type="submit" class="btn btn-block btn-primary">Continue</button>
								</div>
								<p class="help-block text-center">
									By signing up, you agree to our<br />
									<a href="#">Terms</a> &amp; <a href="#">Privacy Policy</a>.
								</p>
							</form>
						</div>
					</div>
					<div class="Box Login">
						<p>Have an account? <a href="#">Log in</a></p>
					</div>							
				</div>
			</div>
		</article>

		<article class="GetTheApp Flex Flex--center">
			<div class="Column-6 hide-on-mobile"></div>
			<div class="Column-6">
				<div class="Download-app">
					<p>Get the app.</p>
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
		</article>
	</main>
@endsection