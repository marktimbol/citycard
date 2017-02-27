@extends('layouts.dashboard')

@section('pageTitle', 'Edit Outlet - '. $outlet->name)

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.outlets.edit', $outlet) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Outlet</h1>
	</div>

	<div id="EditOutlet"></div>

@endsection

@section('footer_scripts')
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU2a80giA7UX_NMcPudNvxfibPRktPEIg&libraries=places"></script>
	<script src="{{ elixir('js/telephone.js') }}"></script>
	<script src="{{ elixir('js/EditOutlet.js') }}"></script>
@endsection
