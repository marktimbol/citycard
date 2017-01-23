@extends('layouts.public')

@section('pageTitle', 'Posts')

@section('bodyClass', 'Posts')

@section('content')
	@include('layouts.public._nav')
	<div class="Posts--container">
		<div class="Posts max-600">
			<div id="PostsInfinite"></div>
		</div>
	</div>
@endsection

@section('footer_scripts')
	<script src="/js/PostsInfinite.js"></script>
@endsection