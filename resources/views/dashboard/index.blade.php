@extends('layouts.dashboard')

@section('pageTitle', 'Dashboard')

@section('content')
	<h1>Welcome Admin!</h1>

	<div class="Statistics">
		<div class="Statistic">
			<h4>{{ $totalMerchants }}<small>Total Merchants</small></h4>
		</div>
		<div class="Statistic">
			<h4>{{ $totalOutlets }}<small>Total Outlets</small></h4>
		</div>
		<div class="Statistic">
			<h4>{{ $totalClerks }}<small>Total Clerks</small></h4>
		</div>
		<div class="Statistic">
			<h4>{{ $totalPosts }}<small>Total Posts</small></h4>
		</div>
		<div class="Statistic">
			<h4>{{ $totalUsers }}<small>Total Users</small></h4>
		</div>
		<div class="Statistic">
			<h4>0<small>Online Users</small></h4>
		</div>
	</div>
@endsection