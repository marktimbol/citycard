@extends('layouts.dashboard')

@section('pageTitle', 'Clerk - '. sprintf('%s %s', $clerk->first_name, $clerk->last_name))

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.clerks.show', $clerk) !!}
@endsection

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">
			Clerk
		</h1>
	</div>

	<div class="row">
		<div class="col-md-2">
			<?php
				$photo = 'http://placehold.it/150x150';
				if( $clerk->photo !== null ) {
					$photo = getPhotoPath($clerk->photo);
				}
			?>

			<div class="has-camera-icon">
				<img src="{{ $photo }}"
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
					Name: {{ $clerk->fullName() }}
					@can('update', $clerk)
						<small>
							<a href="{{ route('dashboard.merchants.clerks.edit', [$merchant->id, $clerk->id]) }}">
								<i class="fa fa-pencil"></i>
							</a>
						</small>
					@endcan					
				</li>
				<li class="list-group-item">
					eMail: {{ $clerk->email }}
				</li>
				<li class="list-group-item">
					Phone: {{ $clerk->phone }}
				</li>
				<li class="list-group-item">
					Address:  {{ sprintf('%s, %s', $clerk->city, $clerk->country) }}
				</li>
				<li class="list-group-item">
					Merchant:
					<a href="{{ route('dashboard.merchants.show', $merchant->id) }}">
						{{ $merchant->name }}
					</a>
				</li>				
			</ul>

			<div class="row">
				<div class="col-md-6">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Can Log in anywhere
							<small>
								<span class="help-block">If activated, we will not check if this user is on the store or no everytime he's logging in on the app.</span>
							</small>					
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Auto Log out
							<small>
								<span class="help-block">Log out user if he/she was away from the store for 10minutes.</span>
							</small>
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Billing
							<small>
								<span class="help-block">Manage billing preferences</span>
							</small>
						</label>
					</div>	

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Messaging
							<small>
								<span class="help-block">Able to chat with users.</span>
							</small>
						</label>
					</div>
				</div>
				<div class="col-md-6">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Reservations
							<small>
								<span class="help-block">Manage Outlet reservations</span>
							</small>
						</label>
					</div>	

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Shop Front
							<small>
								<span class="help-block">Manage Outlet shop front photos</span>
							</small>
						</label>
					</div>	

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Menus
							<small>
								<span class="help-block">Manage Outlet menus</span>
							</small>
						</label>
					</div>	

					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" /> Photos
							<small>
								<span class="help-block">Manage Outlet photos</span>
							</small>
						</label>
					</div>	
				</div>
			</div>		
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
