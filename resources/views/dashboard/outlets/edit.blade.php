@extends('layouts.dashboard')

@section('pageTitle', 'Edit Outlet - '. $outlet->name)

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Update Outlet</h1>
	</div>

	<div id="EditOutlet"></div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
	<script src="/js/EditOutlet.js"></script>
@endsection
