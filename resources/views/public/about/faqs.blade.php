@extends('layouts.public')

@section('pageTitle', 'FAQ')

@section('bodyClass', 'Company--page')

@section('content')
	<nav class="navbar navbar-default">
		<div class="container">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					<img src="/images/logo.svg" alt="CityCard" title="CityCard" class="img-responsive" width="175" height="51" />
				</a>
	    	</div>
	  	</div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="Company__content--container white-bg">
					<div class="Row">
						<div class="Company__nav">
							<h4>About</h4>
							<ul>
								<li><a href="/about/company">Company</a></li>
								<li><a href="#">Jobs</a></li>
								<li><a href="/about/faq">FAQ</a></li>
							</ul>

							<h4>Legal</h4>
							<ul>
								<li><a href="#">Terms</a></li>
								<li><a href="#">Privacy</a></li>
							</ul>	
						</div>					
						<div class="Company__content">
							<div class="row">
								<div class="col-md-12">
									<h1>FAQ</h1>
									<p>This is a short list of our most frequently asked questions. For more information about Instagram, or if you need support, please visit our support center.</p>

									<h3>What is CityCard?</h3>
									<p>
										If you enjoy shopping at outlets across the UAE, the CityCard program adds value to your shopping experience by offering exceptional rewards and benefits. When you sign up, you will receive:
									</p>	
									
									<ul>
										<li>Rewards and Cashback across a wide range of participating outlets.</li>
										<li>Receive offers and discounts exclusive to CityCard membership.</li>
										<li>Special invitations to in-store activities and sales previews.</li>
										<li>Chat feature to enable customers to interact with selected store clerks to advise and assist on any potential purchases.</li>
									</ul>	

									<h3>How do I get started with CityCard?</h3>
									<p>In order to start your loyalty program you need to create an account with us, you can follow the link on “get started” or complete the information on the order confirmation page.</p>

									<h3>Is there a minimum spend amount or a joining fee to become a CityCard member?</h3>
									<p>No. Membership to CityCard is free for life. Just sign up online through our website or at any of our participating outlets and you're ready to enjoy the program.</p>										

									<h3>How do I track my loyalty points?</h3>
									<p>You can track your loyalty points at any time by logging into your account and viewing your profile. This tab will also allow you to redeem points once you have reached enough points for any of the qualifying tiers for each merchant.</p>

									<h3>How do I redeem my points?</h3>
									<p>You can redeem your points through your online account – either purchasing online from the selected list of outlets or by visiting the store of your choose personally.</p>

									<h3>How are the purchase points calculated?</h3>
									<p>The points will be configured as per the configuration of each merchant participating in the program. Loyalty Points are only earned for money spent on products purchased from the CityCard App.</p>

									<h3>Do the points expire?</h3>
									<p>Once your account is activated, your points can be used anytime. The points you earn are not time-sensitive.</p>

									<h3>How long will it take for points to appear on my account?</h3>
									<p>Points will appear on your account the moment the transaction has been made for your purchased item.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>

@endsection