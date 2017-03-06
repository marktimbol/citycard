@extends('layouts.dashboard')

@section('pageTitle', 'Create a Reward')

@section('header_styles')
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Create Reward</h1>
	</div>

	<div id="CreateReward"></div>

	{{-- protected $fillable = ['merchant_id', 'title', 'quantity', 'required_points', 'desc']; --}}
@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/CreateReward.js') }}"></script>
@endsection
