@extends('layouts.dashboard')

@section('pageTitle', 'Add New Outlet')

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.outlets.create', $merchant) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create Outlet</h1>
	</div>

	<div id="CreateOutlet"></div>

@endsection

@section('footer_scripts')
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU2a80giA7UX_NMcPudNvxfibPRktPEIg&libraries=places"></script>
	<script src="/js/telephone.js"></script>
	<script src="{{ elixir('js/CreateOutlet.js') }}"></script>
@endsection
