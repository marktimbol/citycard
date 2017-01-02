<h2>Posts
	<small>
		<a href="{{ route('dashboard.outlets.posts.create', $outlet->id) }}" class="btn btn-sm btn-default">
			Add New
		</a>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Title</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		@forelse( $posts as $post )
		<tr>
			<td>
				<a href="{{ route('dashboard.merchants.posts.show', [$merchant->id, $post->id]) }}">
					{{ $post->title }}
				</a>
			</td>
			<td>
				<form method="POST"
					action="{{ route('dashboard.outlets.posts.destroy', [$outlet->id, $post->id]) }}"
				>
					{{ csrf_field() }}
					{!! method_field('DELETE') !!}
					<button type="submit" class="btn btn-link">
						<i class="fa fa-times-rectangle"></i>
					</button>
				</form>
			</td>
		</tr>

		@empty
		<tr>
			<td colspan="6">No posts yet.</td>
		</tr>
		@endforelse
	</tbody>
</table>