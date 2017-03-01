@extends('layouts.merchant')

@section('content')
	<h2 class="Heading__title">Outlet</h2>

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
				<small>
					<a href="#">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
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
				<small>
					<button 
						class="btn btn-sm btn-link"
						data-toggle="modal"
						data-target="#UpdateOutletAddressModal"
					>					
						<i class="fa fa-pencil"></i>
					</button>
				</small>
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

	@include('merchants.clerks.table', [
		'clerks'	=> $outlet->clerks,
	])

	@include('merchants.for-reservations.table', [
		'items'	=> $outlet->itemsForReservation
	])

	@include('merchants.outlets.posts.table', [
		'outlet_id'	=> $outlet->id,
		'posts'	=> $outlet->posts,
	])

	@include('dashboard._delete', [
		'route'	=> route('clerk.outlets.destroy', [$outlet->id])
	])

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/OutletSettings.js') }}"></script>
@endsection
