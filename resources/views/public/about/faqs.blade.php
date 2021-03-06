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
						@include('public.about._nav')					
						<div class="Company__content">
							<div class="row">
								<div class="col-md-12">
									<h1>FAQ</h1>

									<p>This is a short list of our most frequently asked questions. For more information about CityCard, or if you need support, please visit our support center.</p>

									@forelse( $faqs as $faq )
										<h3>{{ $faq->question }}</h3>
										{!! $faq->answer !!}
									@empty

									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>

@endsection