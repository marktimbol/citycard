@extends('layouts.dashboard')

@section('pageTitle', 'Add New Outlet')

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Add Outlet</h1>
	</div>

	<div id="CreateOutlet"></div>
	
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
	<script src="/js/CreateOutlet.js"></script>
@endsection