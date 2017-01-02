@extends('emails.default')

@section('content')
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
	  		<td>
	  			<h1>Your Reservation was confirmed</h1>

			    <table border="0" cellpadding="0" cellspacing="0" class="table table-bordered table-striped">
			    	<thead>
			    		<tr>
			    			<th>User</th>
			    			<th>Item</th>
			    			<th>Date</th>
			    			<th>Time</th>
			    			<th>Quantity</th>
			    			<th>&nbsp;</th>
			    		</tr>
			    	</thead>
			    	<tbody>
            			<tr>
              				<td>{{ $reservation->user->name }}</td>
              				<td>{{ $reservation->item->title }}</td>
              				<td>{{ $reservation->date->toDateTimeString() }}</td>
              				<td>{{ $reservation->time }}</td>
              				<td>{{ $reservation->quantity }}</td>
              				<td>
              					<span class="label label-success">
              						Confirmed
              					</span>
              				</td>
            			</tr>
			      	</tbody>
			    </table>
	  		</td>
		</tr>
	</table>
@endsection