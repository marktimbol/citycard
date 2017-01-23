@extends('layouts.dashboard')

@section('pageTitle', 'Outlet - '. $outlet->name)

@section('content')
	<div class="Heading">
		<h1 class="Heading__title">Outlet: {{ $outlet->name }}
			@can('update', $outlet)
				<small>
					<a href="{{ route('dashboard.merchants.outlets.edit', [$outlet->merchant->id, $outlet->id]) }}">
						<i class="fa fa-pencil"></i>
					</a>
				</small>
			@endcan
		</h1>
		@include('dashboard._go-back')
	</div>

	<ul class="list-group">
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
			Address: {{ sprintf('%s %s', $outlet->address1, $outlet->address2) }}
		</li>
		<li class="list-group-item">
			Latitude / Longitude: {{ sprintf('%s, %s', $outlet->latitude, $outlet->longitude) }}
		</li>
		<li class="list-group-item">
			Area:  {{ $outlet->getLocation() }}
		</li>
	</ul>

	<ul class="list-group">
		<li class="list-group-item">
			Currency: {{ $outlet->currency }}
		</li>
	</ul>

	<div id="OutletSettings"></div>
	
	<div class="btn-group">
		<button class="btn btn-sm btn-primary btn-has-icon" data-toggle="modal" data-target="#UploadOutletGallery">
			<i class="fa fa-file-photo-o"></i> Manage Shop Front
		</button>
		<a href="#" class="btn btn-sm btn-primary btn-has-icon">
			<i class="fa fa-key"></i> Change Password
		</a>
	</div>

	{{-- @include('dashboard.outlets.clerks._all') --}}
	@include('dashboard.merchants._clerks', [
		'clerks'	=> $outletClerks
	])

	@include('dashboard.outlets.items-for-reservation._all', [
		'items'	=> $itemsForReservation
	])

	@include('dashboard.merchants._posts')

	@include('dashboard.outlets._upload-outlet-gallery')
	@include('dashboard.outlets._select-existing-clerks')

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.outlets.destroy', [$merchant->id, $outlet->id])
	])

@endsection

@section('footer_scripts')
	<script src="{{ elixir('js/MerchantPosts.js') }}"></script>
	<script src="{{ elixir('js/OutletSettings.js') }}"></script>
	<script src="{{ elixir('js/CreateItemForReservation.js') }}"></script>
	<script src="{{ elixir('js/ItemsForReservation.js') }}"></script>
@endsection
