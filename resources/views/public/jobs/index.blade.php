@extends('layouts.public')

@section('pageTitle', 'Jobs')

@section('bodyClass', 'Jobs--page')

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
						@include('public.about._nav')				
						<div class="Company__content">
							<div class="row">
								<div class="col-md-12">
									<h1>Jobs</h1>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>

@endsection