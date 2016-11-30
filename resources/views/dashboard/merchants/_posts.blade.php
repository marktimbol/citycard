<h2>Posts
	<small>
		<a href="{{ route('dashboard.merchants.posts.create', $merchant->id) }}" class="btn btn-sm btn-default">
			Add New
		</a>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Type</th>
			<th>Title</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $posts as $post )
		<tr>
			<td>{{ ucfirst($post->type) }}</td>
			<td>
				<a href="{{ route('dashboard.merchants.posts.show', [$merchant->id, $post->id]) }}">
					{{ $post->title }}
				</a>
			</td>
			<td>
				<form method="POST" action="">

				</form>
				
				<a href="{{ route('dashboard.merchants.posts.edit', [$merchant->id, $post->id]) }}" 
					class="btn btn-sm btn-default"
				>
					Edit
				</a>
			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No record yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>