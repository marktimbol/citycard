@extends('layouts.public')

@section('pageTitle', $outlet->name)

@section('content')
	@include('layouts.public._nav')

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="UserProfile--container">
					<div class="UserProfile__header">
						<div class="Column-4 Flex Flex--center">
							<img src="{{ getPhotoPath($outlet->merchant->logo) ?: '/images/no-image.jpg' }}" alt="" title="" class="img-responsive img-circle" />
						</div>
						<div class="Column-6">
							<div class="Flex Flex--center Space-between">
								<h1>{{ $outlet->name }}</h1>
								<div class="Flex Flex--center">
									<button class="btn btn-default margin-right-10">Following</button>
									<a href="#" class="citycard-icon icon-more">
					                    Events
					                </a>
								</div>
							</div>

							<div class="Row">
								<div class="margin-right-20">
									<p class="lead">
										{{ sprintf('%s %s', $outlet->posts()->count(), str_plural('post', $outlet->posts()->count()) )}}
									</p>
								</div>

								<div>
									<p class="lead">
										{{ sprintf('%s %s', $outlet->members()->count(), str_plural('follower', $outlet->members()->count()) )}}
									</p>								
								</div>
							</div>

							<div class="Row">
								<p>
									{{ $outlet->address }}
								</p>
							</div>				
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('footer_scripts')

@endsection