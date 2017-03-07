@extends('layouts.dashboard')

@section('pageTitle', 'Outlet - '. $outlet->name)

@section('header_styles')
	<link href="{{ elixir('css/select.css') }}" rel="stylesheet">
@endsection

@section('breadcrumbs')
	{!! Breadcrumbs::render('merchants.outlets.show', $outlet) !!}
@endsection

@section('content')
	
	<div class="Heading">
		<h1 class="Heading__title">Outlet</h1>
	</div>

	<div class="col-md-2">
		<?php
			$logo = 'http://placehold.it/150x150';
			if( $outlet->merchant->logo !== null ) {
				$logo = getPhotoPath($outlet->merchant->logo);
			}
		?>
		<div class="has-camera-icon">
			<img src="{{ $logo }}"
				alt="{{ $outlet->name }}"
				title="{{ $outlet->name }}"
				class="img-responsive img-circle" />
			<button class="btn btn-sm btn-link" data-toggle="modal" data-target="#ChangeMerchantPhoto">
				<i class="fa fa-camera fa-2x"></i>
			</button>
		</div>
	</div>
	<div class="col-md-10">
		<ul class="list-group">
			<li class="list-group-item">
				Name: {{ $outlet->name }}
				@can('update', $outlet)
					<small>
						<a href="{{ route('dashboard.merchants.outlets.edit', [$outlet->merchant->id, $outlet->id]) }}">
							<i class="fa fa-pencil"></i>
						</a>
					</small>
				@endcan			
			</li>
			<li class="list-group-item">
				Merchant: 
				<a href="{{ route('dashboard.merchants.show', $outlet->merchant->id) }}">
					{{ $outlet->merchant->name }}
				</a>
			</li>
			<li class="list-group-item">
				eMail: {{ $outlet->email }} <label class="label label-danger">Not Verified</label>
			</li>
			<li class="list-group-item">
				Phone: {{ $outlet->phone }} <label class="label label-danger">Not Verified</label>
			</li>
		</ul>

		<ul class="list-group">
			<li class="list-group-item">
				Address: {{ $outlet->address }}
				@can('update', $outlet)
					<small>
						<button 
							class="btn btn-sm btn-link"
							data-toggle="modal"
							data-target="#UpdateOutletAddressModal"
						>					
							<i class="fa fa-pencil"></i>
						</button>
					</small>
				@endcan					
			</li>
			<li class="list-group-item">
				Area:  {{ $outlet->getLocation() }}
			</li>
		</ul>

		<ul class="list-group">
			<li class="list-group-item">
				Categories: 
				@foreach( $outlet->categories as $category )
					<span class="label label-success">{{ $category->name }}</span>
				@endforeach
			</li>
			<li class="list-group-item">
				Sub-categories:
				@foreach( $outlet->subcategories as $category )
					<span class="label label-success">{{ $category->name }}</span>
				@endforeach				
			</li>
		</ul>		

		<ul class="list-group">
			<li class="list-group-item">
				Currency: {{ $outlet->currency }}
			</li>
		</ul>
	</div>

	<div id="OutletSettings"></div>
	
	<div class="btn-group">
		<button class="btn btn-sm btn-primary btn-has-icon" data-toggle="modal" data-target="#UploadOutletGallery">
			<i class="fa fa-file-photo-o"></i> Manage Shop Front
		</button>
	</div>

	{{-- @include('dashboard.outlets.clerks._all') --}}
	@include('dashboard.merchants._clerks', [
		'clerks'	=> $outletClerks
	])

	@include('dashboard.outlets.items-for-reservation._all', [
		'items'	=> $itemsForReservation
	])

	@include('dashboard.merchants._posts')

	@include('dashboard.merchants._rewards', [
		'rewards'	=> $rewards,
	])

	@include('dashboard.merchants._vouchers', [
		'vouchers'	=> $vouchers,
	])

	@include('dashboard.outlets._update-outlet-address')
	@include('dashboard.outlets._upload-outlet-gallery')
	@include('dashboard.outlets._select-existing-clerks')

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.outlets.destroy', [$merchant->id, $outlet->id])
	])

@endsection

@section('footer_scripts')
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDU2a80giA7UX_NMcPudNvxfibPRktPEIg&libraries=places"></script>
	<script src="{{ elixir('js/MerchantPosts.js') }}"></script>
	<script src="{{ elixir('js/OutletSettings.js') }}"></script>
	<script src="{{ elixir('js/CreateItemForReservation.js') }}"></script>
	<script src="{{ elixir('js/ItemsForReservation.js') }}"></script>
	<script src="{{ elixir('js/UpdateOutletAddress.js') }}"></script>
@endsection
