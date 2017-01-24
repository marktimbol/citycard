@extends('layouts.public')

@section('pageTitle', $user->name)

@section('content')
	@include('layouts.public._nav')

	<div class="container Citycard--container">
		<div class="row">
			<div class="col-md-12">
				<h1>Hello {{ auth()->user()->name }}</h1>
				<p>Email: {{ auth()->user()->email }}</p>
				
				<form method="POST" action="/logout">
					{{ csrf_field() }}
					<div class="form-group">
						<button class="btn btn-sm btn-danger">Logout</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')

@endsection