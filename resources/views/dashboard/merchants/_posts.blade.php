<h2>Posts
	<small>
		<a href="{{ route('dashboard.merchants.posts.create', $merchant->id) }}" class="btn btn-sm btn-default">
			Add New
		</a>
	</small>
</h2>

<div id="MerchantPosts"></div>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Type</th>
			<th>Title</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@forelse( $posts as $post )
		<tr>
			<td>
				{{ ucfirst($post->type) }}
			</td>
			<td>
				<a href="{{ route('dashboard.merchants.posts.show', [$merchant->id, $post->id]) }}">
					{{ $post->title }}
				</a>		
				@if( $post->type == 'events' )
					<br />
					@if( $post->event_date->toDateString() < \Carbon\Carbon::now()->toDateString() )
						<span class="label label-danger">
					@else
						<span class="label label-success">
					@endif			
					Event Date:			
					{{ $post->event_date->toFormattedDateString() }} &mdash; {{ $post->event_time }}
					</span>
				@endif						
			</td>
			<td>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="unpublish" value="{{ $post->id }}" /> Publish
					</label>
				</div>
			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>