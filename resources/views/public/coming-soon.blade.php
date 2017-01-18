@extends('layouts.public')

@section('bodyClass', 'About')

@section('content')
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="/" class="navbar-brand">
					{{ config('app.name') }}
				</a>
	    	</div>
	  	</div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h1 class="text-center">Coming soon</h1>
			</div>
		</div>	
	</div>

@endsection