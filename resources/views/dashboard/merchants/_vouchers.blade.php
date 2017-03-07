<h2>Vouchers</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Title</th>
			<th>Verification Code</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@forelse( $vouchers as $voucher )
		<tr>
			<td>{{ $voucher->reward->title }}</td>
			<td>{{ $voucher->verification_code }}</td>
			<td>
				@if( $voucher->redeemed )
					<span class="label label-success">Redeemed</span>
				@else
					<span class="label label-danger">Not yet Redeemed</span>
				@endif
			</td>
		</tr>
		@empty
		<tr>
			<td colspan="3">There's no record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>