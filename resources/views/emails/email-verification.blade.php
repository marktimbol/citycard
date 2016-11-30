@extends('emails.default')

@section('content')
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
	  		<td>
	  			<h1>Verify Your Email Address</h1>
	    		<p>You're human right? Or humanoid at least?</p>

	    		<p>We do this to make sure you'll get the emails that you choose to receive from {{ config('app.name') }}, and also to make sure you're a human.</p>

			    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
			    	<tbody>
			        	<tr>
			          		<td align="left">
			            		<table border="0" cellpadding="0" cellspacing="0">
			              			<tbody>
			                			<tr>
			                  				<td>
			                  					<a href="{{ url('register/confirm/'. $user->token) }}">
			                  						Confirm your email address
			                  					</a>
			                  				</td>
			                			</tr>
			             			</tbody>
			            		</table>
			          		</td>
			        	</tr>
			      	</tbody>
			    </table>
	  		</td>
		</tr>
	</table>
@endsection