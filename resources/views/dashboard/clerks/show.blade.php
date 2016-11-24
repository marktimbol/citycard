@extends('layouts.dashboard')

@section('pageTitle', 'Clerk - '. sprintf('%s %s', $clerk->first_name, $clerk->last_name))

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">
			Clerk: {{ sprintf('%s %s', $clerk->first_name, $clerk->last_name) }}
		</h1>
		@include('dashboard._go-back')
	</div>

	<div class="row">
		<div class="col-md-2">
			@if( $clerk->photo !== '' )
				<div class="has-camera-icon">
					<img src="{{ getPhotoPath($clerk->photo) }}"
						alt="{{ $clerk->fullName() }}"
						title="{{ $clerk->fullName() }}"
						class="img-responsive img-circle" />
					<button class="btn btn-sm btn-link" data-toggle="modal" data-target="#ChangeClerkPhoto">
						<i class="fa fa-camera fa-2x"></i>
					</button>
				</div>

				<div class="modal fade" id="ChangeClerkPhoto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
				    	<div class="modal-content">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title" id="myModalLabel">Change Photo</h4>
				      		</div>
					    	<div class="modal-body">
								<form class="dropzone" method="POST" action="/dashboard/clerks/{{$clerk->id}}/photos">
									{{ csrf_field() }}
									{{ method_field('PUT') }}
								</form>
							</div>
				      		<div class="modal-footer">
				    			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				      		</div>
				    	</div>
				  	</div>
				</div>

			@else
				<form method="POST" class="dropzone" action="/dashboard/clerks/{{$clerk->id}}/photos">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
				</form>
			@endif

			<br />
			<p>
				<a href="#" class="btn btn-sm btn-primary btn-block">
					Change Password
				</a>
			</p>

		</div>
		<div class="col-md-10">
			<ul class="list-group">
				<li class="list-group-item">
					eMail: {{ $clerk->email }}
				</li>
				<li class="list-group-item">
					Phone: {{ $clerk->phone }}
				</li>
				<li class="list-group-item">
					Address:  {{ sprintf('%s, %s', $clerk->city, $clerk->country) }}
				</li>
			</ul>
		</div>
	</div>

	@include('dashboard._working-in', [
		'outlets' => $outlets
	])

	{{-- @include('dashboard._change-password') --}}

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.clerks.destroy', [$merchant->id, $clerk->id])
	])
@endsection
