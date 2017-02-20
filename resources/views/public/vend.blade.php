@extends('layouts.public')

@section('pageTitle', 'Vend')

@section('content')
	@include('layouts.public._nav')

	<div class="container max-600">
		<div class="row">
			<div class="col-md-12">
				<div id="Vend"></div>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/Vend.js') }}"></script>
@endsection