@extends('layouts.public')

@section('pageTitle', $user->name)

@section('content')
	@include('layouts.public._nav')

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="UserProfile--container">
					<div class="UserProfile__header">
						<div class="Column-4 Flex Flex--center">
							<img src="/images/no-image.jpg" alt="" title="" class="img-responsive img-circle" />
						</div>
						<div class="Column-6">
							<h1>{{ auth()->user()->name }}</h1>

							<p class="lead_">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
								tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.
							</p>

							<form method="POST" action="/logout">
								{{ csrf_field() }}
								<div class="form-group">
									<button class="btn btn-sm btn-danger">Logout</button>
								</div>
							</form>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')

@endsection