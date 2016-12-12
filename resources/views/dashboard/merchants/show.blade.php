@extends('layouts.dashboard')

@section('pageTitle', $merchant->name)

@section('content')
	<?php
		$area = $merchant->areas->first();
	?>
	<div class="Heading">
		<h1 class="Heading__title">{{ $merchant->name }}</h1>
		@include('dashboard._go-back')
	</div>

	<div class="row">
		<div class="col-md-2">
			@if( $merchant->logo !== '' )
				<div class="has-camera-icon">
					<img src="{{ getPhotoPath($merchant->logo) }}"
						alt="{{ $merchant->name }}"
						title="{{ $merchant->name }}"
						class="img-responsive img-circle" />
					<button class="btn btn-sm btn-link" data-toggle="modal" data-target="#ChangeMerchantPhoto">
						<i class="fa fa-camera fa-2x"></i>
					</button>
				</div>

				<div class="modal fade" id="ChangeMerchantPhoto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					<div class="modal-dialog" role="document">
				    	<div class="modal-content">
				      		<div class="modal-header">
				        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				        		<h4 class="modal-title" id="myModalLabel">Change Photo</h4>
				      		</div>
					    	<div class="modal-body">
								<form class="dropzone" method="POST" action="/dashboard/merchants/{{$merchant->id}}/photos">
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
				<form method="POST" class="dropzone" action="/dashboard/merchants/{{$merchant->id}}/photos">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
				</form>
			@endif
		</div>
		<div class="col-md-10">
			<ul class="list-group">
				<li class="list-group-item">
					eMail: {{ $merchant->email }} <label class="label label-danger">Not Verified</label>
				</li>
				<li class="list-group-item">
					Phone: {{ $merchant->phone }} <label class="label label-danger">Not Verified</label>
				</li>
				<li class="list-group-item">
					Address: {{ sprintf('%s - %s, %s', $area->name, $area->city->name, $area->city->country->name) }}
				</li>
			</ul>

			<ul class="list-group">
				@foreach( $categories as $category )
					<li class="list-group-item">
						Category: {{ $category->name }}
					</li>
					<li class="list-group-item">
						Sub-Categories:
						@foreach( $category->subcategories as $subcategory )
							<label class="label label-success">{{ $subcategory->name }}</label>
						@endforeach
					</li>
				@endforeach
			</ul>
		</div>
	</div>

	<a href="#" class="btn btn-sm btn-primary">
		Change Password
	</a>

	@include('dashboard.merchants._posts')

	@include('dashboard.merchants._outlets')

	@include('dashboard.merchants._clerks')

	@if( adminCan('update', $merchant) )
		@include('dashboard._delete', [
			'route'	=> route('dashboard.merchants.destroy', $merchant->id)
		])
	@endif
@endsection
