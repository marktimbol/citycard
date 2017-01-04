@extends('emails.default')

@section('content')
	<table border="0" cellpadding="0" cellspacing="0">
		<tr>
	  		<td>
	  			<h1>You we're invitation by {{ $invitation->user->name }} to download the City Card app</h1>

	  			<p><a href="#">Download City Card from App Store</a></p>
	  			<p><a href="#">Download City Card from Google Play</a></p>
	  		</td>
		</tr>
	</table>
@endsection