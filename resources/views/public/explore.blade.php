@extends('layouts.public')

@section('pageTitle', 'Explore Merchants')

@section('bodyClass', 'Explore--page')

@section('content')
	@include('layouts.public._nav')

	<div class="container max-600">
		<div class="row">
			<div class="col-md-12">
				<div class="Explore--container">
					<div class="Explore__title--container">
						<h1 class="Explore__title">Discover Merchants</h1>
					</div>
					<div className="Explore__content--container">
						<div id="ExploreMerchants"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/ExploreMerchants.js') }}"></script>
@endsection