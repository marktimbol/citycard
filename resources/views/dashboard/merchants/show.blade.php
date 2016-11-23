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
		<div class="col-md-8">
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
		<div class="col-md-4">
			@if( $merchant->logo !== '' )
				<img src="{{ getPhotoPath($merchant->logo) }}"
					alt="{{ $merchant->name }}"
					title="{{ $merchant->name }}"
					class="img-responsive" />
			@endif
			<form method="POST" class="dropzone" action="/dashboard/merchants/{{$merchant->id}}/photos">
				{{ csrf_field() }}
				{{ method_field('PUT') }}
			</form>
		</div>
	</div>

	<a href="#" class="btn btn-sm btn-primary">
		Change Password
	</a>

	@include('dashboard.merchants._posts')

	@include('dashboard.merchants._outlets')

	@include('dashboard.merchants._clerks')

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.destroy', $merchant->id)
	])
@endsection
