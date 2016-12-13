@extends('layouts.dashboard')

@section('pageTitle', 'Dashboard')

@section('content')
	<h1>Welcome {{ auth()->guard('admin')->user()->name }}!</h1>

{{-- 	<h3>Merchants</h3>
	<canvas id="merchants_statistics" width="400" height="200"></canvas>
	<h3>Outlets</h3>
	<canvas id="outlets_statistics" width="400" height="200"></canvas>
	<h3>Posts</h3>
	<canvas id="posts_statistics" width="400" height="200"></canvas> --}}

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
			<h4>{{ $totalNewsFeed }}<small>Total News Feed</small></h4>
		</div>		
		<div class="Statistic">
			<h4>{{ $totalDeals }}<small>Total Deals</small></h4>
		</div>	
		<div class="Statistic">
			<h4>{{ $totalEvents }}<small>Total Events</small></h4>
		</div>	
		<div class="Statistic">
			<h4>{{ $totalUsers }}<small>Total Users</small></h4>
		</div>
		<div class="Statistic">
			<h4>0<small>Online Users</small></h4>
		</div>
	</div>
@endsection

@section('footer_scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.js"></script>

	<script>
		var data = {
		    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
		    datasets: [
		        {
		            label: "Statistics",
		            backgroundColor: [
		                'rgba(255, 99, 132, 0.2)',
		                'rgba(54, 162, 235, 0.2)',
		                'rgba(255, 206, 86, 0.2)',
		                'rgba(75, 192, 192, 0.2)',
		                'rgba(153, 102, 255, 0.2)',
		                'rgba(255, 159, 64, 0.2)',
		                'rgba(255, 99, 132, 0.2)',
		                'rgba(54, 162, 235, 0.2)',
		                'rgba(255, 206, 86, 0.2)',
		                'rgba(75, 192, 192, 0.2)',
		                'rgba(153, 102, 255, 0.2)',
		                'rgba(255, 159, 64, 0.2)'		                
		            ],
		            borderColor: [
		                'rgba(255,99,132,1)',
		                'rgba(54, 162, 235, 1)',
		                'rgba(255, 206, 86, 1)',
		                'rgba(75, 192, 192, 1)',
		                'rgba(153, 102, 255, 1)',
		                'rgba(255, 159, 64, 1)',
		                'rgba(255, 99, 132, 0.2)',
		                'rgba(54, 162, 235, 0.2)',
		                'rgba(255, 206, 86, 0.2)',
		                'rgba(75, 192, 192, 0.2)',
		                'rgba(153, 102, 255, 0.2)',
		                'rgba(255, 159, 64, 0.2)'		                
		            ],
		            borderWidth: 1,
		            data: [65, 59, 80, 81, 56, 55, 52, 65, 59, 80, 81, 56],
		        }
		    ]
		};

		var options = {
			title: {
				display: false,
				text: 'Custom Chart Title'
			},
			legend: {
				display: false
			},
			scales: {
				yAxes: [{
					ticks: {
						// type: 'logarithmic',
						beginAtZero: true,
						max: 1000,
						stepSize: 200,
					}
				}]
			}
		}

		var merchantChart = document.getElementById('merchants_statistics');
		var merchantsBar = new Chart(merchantChart, {
			type: 'bar',
			data: data,
			options: options,
		});

		var outletChart = document.getElementById('outlets_statistics');
		var outletsBar = new Chart(outletChart, {
			type: 'bar',
			data: data,
			options: options
		});		

		var postChart = document.getElementById('posts_statistics');
		var postsBar = new Chart(postChart, {
			type: 'bar',
			data: data,
			options: options
		});				
	</script>
@endsection