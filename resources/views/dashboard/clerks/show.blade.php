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
				<div class="">
					<img src="{{ getPhotoPath($clerk->photo) }}"
						alt="{{ $clerk->fullName() }}"
						title="{{ $clerk->fullName() }}"
						class="img-responsive img-circle" />
					<span>
						<a href="">
							<i class="fa fa-camera"></i>
						</a>
					</span>
				</div>
			@else
				<form method="POST" class="dropzone" action="/dashboard/clerks/{{$clerk->id}}/photos">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
				</form>
			@endif

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

	<a href="#" class="btn btn-sm btn-primary">
		Change Password
	</a>

	@include('dashboard._working-in', [
		'outlets' => $outlets
	])

	{{-- @include('dashboard._change-password') --}}

	@include('dashboard._delete', [
		'route'	=> route('dashboard.merchants.clerks.destroy', [$merchant->id, $clerk->id])
	])
@endsection
