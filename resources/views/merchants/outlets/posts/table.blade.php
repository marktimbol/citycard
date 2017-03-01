<h2>Posts
	<small>
		<a href="#" class="btn btn-sm btn-default">
			Add New
		</a>
	</small>
</h2>

<table class="table table-bordered">
	<thead>
		<tr>
			<th>Type</th>
			<th>Title</th>
		</tr>
	</thead>
	<tbody>
		@foreach( $posts as $post)
		<tr>
			<td width="100">{{ ucfirst($post->type) }}</td>
			<td>
				<a href="{{ route('clerk.posts.show', $post->id) }}">
					{{ $post->title }}
				</a>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>