@extends('layouts.merchant')

@section('content')
	<h2 class="Heading__title">Merchant</h2>

	<div class="row">
		<div class="col-md-2">
			<div class="has-camera-icon">
				<img src="http://placehold.it/150x150" 
					alt="{{ $merchant->name }}" 
					title="{{ $merchant->nae }}" 
					class="img-responsive img-circle" />
				<button class="btn btn-sm btn-link" data-toggle="modal" data-target="#ChangeMerchantPhoto">
					<i class="fa fa-camera fa-2x"></i>
				</button>
			</div>
		</div>	
		<div class="col-md-10">
			<ul class="list-group">
				<li class="list-group-item">
					Name: {{ $merchant->name }}
					<small>
						<a href="#">
							<i class="fa fa-pencil"></i>
						</a>
					</small>					
				</li>
				<li class="list-group-item">
					eMail: {{ $merchant->email }} <label class="label label-danger">Not Verified</label>
				</li>
				<li class="list-group-item">
					Phone: {{ $merchant->phone }} <label class="label label-danger">Not Verified</label>
				</li>
				<li class="list-group-item">
					Address: {{ $merchant->address }}
				</li>		
			</ul>

			<ul class="list-group">
				<li class="list-group-item">
					Points Percentage: 100%
				</li>
				<li class="list-group-item">
					Cashback Percentage: 100%
				</li>				
			</ul>

			<ul class="list-group">
				@foreach( $merchant->categories as $category )
					<li class="list-group-item">
						Category: {{ $category->name }}
					</li>
				@endforeach
				<li class="list-group-item">
					Currency: {{ $merchant->currency }}
				</li>
			</ul>

		</div>
	</div>
@endsection