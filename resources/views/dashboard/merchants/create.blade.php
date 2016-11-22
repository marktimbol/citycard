@extends('layouts.dashboard')

@section('pageTitle', 'Add New Merchant')

@section('header_styles')
	<link href="{{ elixir('css/telephone.css') }}" rel="stylesheet">
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create Merchant</h1>
	</div>

	<div id="CreateMerchant"></div>

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/telephone.js') }}"></script>
	<script src="/js/CreateMerchant.js"></script>
@endsection
