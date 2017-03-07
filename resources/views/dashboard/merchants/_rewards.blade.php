<h2>Rewards</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Title</th>
			<th>Required Points</th>
			<th>Remaining</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $rewards as $reward )
		<tr>
			<td>
				{{ $reward->title }}<br />
				Available in:<br />
				@foreach( $reward->outlets as $outlet)
					<span class="label label-success">{{ $outlet->name }}</span>
				@endforeach
			</td>
			<td>{{ $reward->required_points }}</td>
			<td>{{ $reward->quantity }}</td>
		</tr>
		@empty
			<tr>
				<td colspan="3">There's no record yet.</td>
			</tr>
		@endforelse
	</tbody>
</table>